<?php


namespace App\Modules\UserManagement\Messenger\Queries;


use App\Entity\Security\User;
use App\Repository\Security\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Service\Attribute\Required;

class GetCurrentUserHandler
{
    private TokenStorageInterface $tokenStorage;
    private UserRepository $repository;

    public function __invoke(GetCurrentUser $query): ?User
    {
        $tokenUser = $this->tokenStorage->getToken()->getUser();

        if ($tokenUser instanceof User) {
            return $tokenUser;
        }

        return null;
    }

    #[Required]
    public function setTokenStorage(TokenStorageInterface $tokenStorage): void
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Required]
    public function setRepository(UserRepository $repository): void
    {
        $this->repository = $repository;
    }
}