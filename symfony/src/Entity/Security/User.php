<?php

namespace App\Entity\Security;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Contracts\Security\Enum\Permission;
use App\Contracts\UserManagement\Enum\UserStatus;
use App\Modules\UserManagement\GraphQL\Resolver\GetCurrentUserResolver;
use App\Modules\UserManagement\GraphQL\Resolver\OnboardUserResolver;
use App\Modules\UserManagement\Messenger\Commands\SignUserUp;
use App\Repository\Security\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @Vich\Uploadable
 */
#[ApiResource(
    collectionOperations: [],
    graphql: [
        'create' => [
            'input' => SignUserUp::class,
            'messenger' => 'input',
        ],
        'collection_query',
        'current' => [
            'item_query' => GetCurrentUserResolver::class,
            'args' => [],
            'security' => 'is_granted(constant("\\\App\\\Contracts\\\Security\\\Enum\\\Permission::USER_VIEW_SELF"), object)',
            'normalization_context' => [
                'groups' => [
                    'user:self',
                ],
            ],
        ],
        'onboard' => [
            'security_post_denormalize' => 'is_granted(constant("\\\App\\\Contracts\\\Security\\\Enum\\\Permission::USER_EDIT_SELF"), object)',
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
            'security' => 'is_granted(constant("\\\App\\\Contracts\\\Security\\\Enum\\\Permission::USER_VIEW_SELF"))',
            'normalization_context' => [
                'groups' => [
                    'user:self',
                ],
            ],
        ],
    ],
)]
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @Groups("user:self")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("user:self")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("user:self")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups("user:self")
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
     * @var UserStatus
     * @Groups("user:self")
     */
    private $status;

    /**
     * @Groups("user:self")
     */
    public ?string $profilePictureUrl = null;

    /**
     * @var File|null
     *                Used by vich uploadable to upload files
     *
     * @Vich\UploadableField(mapping="user_profile_picture", fileNameProperty="profilePicture")
     */
    public $profilePictureFile;

    private function __construct()
    {
        $this->status = UserStatus::get(UserStatus::SIGNED_UP);
        $this->roles = new ArrayCollection();
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

        $self
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setPassword($password)
            ->setEmail($email);

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

        $self->setEmail($email)
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setStatus(UserStatus::get(UserStatus::SIGNED_UP_OAUTH));

        return $self;
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
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

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->getEmail();
    }

    public function getEmail(): ?string
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

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

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

    public function setGithubId(?string $githubId): self
    {
        $this->githubId = $githubId;

        return $this;
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function setStatus(UserStatus $status): self
    {
        $this->status = $status;

        return $this;
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
}
