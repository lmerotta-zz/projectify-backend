<?php

namespace App\Entity\Security;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Contracts\Security\Enum\Permission;
use App\Contracts\UserManagement\Enum\UserStatus;
use App\Entity\UserManagement\Team;
use App\Modules\Common\ApiPlatform\Filter\ExcludeFilter;
use App\Modules\UserManagement\ApiPlatform\Filter\UserEmailFilter;
use App\Modules\UserManagement\GraphQL\Resolver\GetCurrentUserResolver;
use App\Modules\UserManagement\GraphQL\Resolver\OnboardUserResolver;
use App\Modules\UserManagement\Messenger\Commands\SignUserUp;
use App\Modules\UserManagement\Model\UserDTO;
use App\Repository\Security\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Vich\Uploadable
 */
#[ApiResource(
    collectionOperations: [],
    graphql: [
        'collection_query' => [
            'security' => 'is_granted(permission("USER_VIEW"))',
        ],
        'create' => [
            'input' => SignUserUp::class,
            'messenger' => 'input',
        ],
        'current' => [
            'item_query' => GetCurrentUserResolver::class,
            'args' => [],
            'security' => 'is_granted(permission("USER_VIEW"), object)',
        ],
        'onboard' => [
            'security_post_denormalize' => 'is_granted(permission("USER_EDIT"), object)',
            'mutation' => OnboardUserResolver::class,
            'deserialize' => false,
            'args' => [
                'picture' => [
                    'type' => 'Upload!',
                    'description' => 'Profile picture file',
                ],
                'firstName' => [
                    'type' => 'String!',
                    'description' => 'First name of the user',
                ],
                'lastName' => [
                    'type' => 'String!',
                    'description' => 'Last name of the user',
                ],
            ],
        ],
    ],
    itemOperations: [
        'get' => [
            'security' => 'is_granted(permission("USER_VIEW"), object)',
        ],
    ],
    output: UserDTO::class,
)]
#[ApiFilter(UserEmailFilter::class)]
#[ApiFilter(ExcludeFilter::class, properties: ["teams"])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profilePicture;

    /**
     * @ORM\ManyToMany(targetEntity=Role::class, cascade={"persist", "remove"})
     * @ORM\JoinTable(
     *     name="users_roles",
     *     joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="role_name", referencedColumnName="name")}
     * )
     */
    private $roles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $githubId;

    /**
     * @ORM\Column(type="user_status")
     *
     * @var UserStatus
     */
    private $status;

    /**
     * @var File|null
     *                Used by vich uploadable to upload files
     *
     * @Vich\UploadableField(mapping="user_profile_picture", fileNameProperty="profilePicture")
     */
    public $profilePictureFile;

    /**
     * @ORM\ManyToMany(targetEntity=Team::class, mappedBy="members")
     */
    private $teams;

    private function __construct()
    {
        $this->status = UserStatus::get(UserStatus::SIGNED_UP);
        $this->roles = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    public static function create(
        UuidInterface $id,
        string $firstName,
        string $lastName,
        string $password,
        string $email
    ): self {
        $self = new static();
        $self->id = $id;
        $self->firstName = $firstName;
        $self->lastName = $lastName;
        $self->email = $email;
        $self->password = $password;

        return $self;
    }

    public static function createFromOAuth(
        UuidInterface $id,
        string $firstName,
        string $lastName,
        string $email
    ): self {
        $self = new static();
        $self->id = $id;
        $self->email = $email;
        $self->firstName = $firstName;
        $self->lastName = $lastName;
        $self->status = UserStatus::get(UserStatus::SIGNED_UP_OAUTH);

        return $self;
    }

    public function onboard(string $confirmedFirstName, string $confirmedLastName, ?File $profilePicture = null): void
    {
        $this->profilePictureFile = $profilePicture;
        $this->firstName = $confirmedFirstName;
        $this->lastName = $confirmedLastName;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function addRole(Role $role): self
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(Role $role): self
    {
        $this->roles->removeElement($role);

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPermissions(): Permission
    {
        return Permission::get(array_reduce(
            $this->getInternalRoles()->toArray(),
            static fn (int $carry, Role $item) => $carry | $item->getPermissions()->getValue(),
            0
        ));
    }

    // ------ UserInterface methods ----- //

    public function eraseCredentials(): void
    {
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->getInternalRoles()->map(static fn (Role $r) => $r->getName())->toArray();
    }

    /**
     * @return Collection|Role[]
     */
    public function getInternalRoles(): Collection
    {
        return $this->roles;
    }

    // ------ end UserInterface methods ----- //

    public function getGithubId(): ?string
    {
        return $this->githubId;
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    #[Pure]
    public function getUsername(): string
    {
        return $this->getEmail();
    }

    #[Pure]
    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }

    public function getSalt(): ?string
    {
        return null;
    }

    // ---------- Workflow user journey ----------

    public function getStringStatus(): string
    {
        return $this->status->getValue();
    }

    public function setStringStatus(string $stringStatus): User
    {
        $this->status = UserStatus::get($stringStatus);

        return $this;
    }

    /**
     * @return Collection|Team[]
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams[] = $team;
            $team->addMember($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->removeElement($team)) {
            $team->removeMember($this);
        }

        return $this;
    }
}
