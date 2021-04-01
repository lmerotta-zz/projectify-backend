<?php

namespace App\Entity\Security;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Contracts\Security\Enum\Permission;
use App\Modules\UserManagement\Messenger\Commands\SignUserUp;
use App\Repository\Security\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
#[ApiResource(
    collectionOperations: [],
    graphql: [
        'create' => [
            'input' => SignUserUp::class,
            'messenger' => 'input'
        ],
        'item_query' => [
            'security' => 'is_granted(constant("\\\App\\\Contracts\\\Security\\\Enum\\\Permission::USER_VIEW_SELF"))',
            'normalization_context' => [
                'groups' => [
                    'user:self'
                ]
            ]
        ]
    ],
    itemOperations: [
        'get' => [
            'security' => 'is_granted(constant("\\\App\\\Contracts\\\Security\\\Enum\\\Permission::USER_VIEW_SELF"))',
            'normalization_context' => [
                'groups' => [
                    'user:self'
                ]
            ]
        ]
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
     * @ORM\Column(type="string", length=255)
     * @Groups("user:self")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("user:self")
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

    private function __construct()
    {
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

        $self
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setEmail($email);

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
}
