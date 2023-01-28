<?php

namespace Sword\SwordBundle\Security;

use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Williarin\WordpressInterop\Bridge\Entity\Option;
use Williarin\WordpressInterop\Bridge\Entity\User as InteropUser;
use Williarin\WordpressInterop\Criteria\SelectColumns;
use Williarin\WordpressInterop\EntityManagerInterface as InteropEntityManagerInterface;

class UserProvider implements UserProviderInterface
{
    public function __construct(
        private readonly DoctrineEntityManagerInterface $doctrine,
        private readonly InteropEntityManagerInterface $interop,
        #[Autowire('%sword.table_prefix%')] private readonly string $tablePrefix,
    ) {
    }

    public function loadUserByIdentifier($identifier): UserInterface
    {
        $user = $this->doctrine->getRepository(User::class)
            ->findOneBy([
                'login' => $identifier
            ]);

        $userInterop = $this->interop->getRepository(InteropUser::class)
            ->findOneBy([
                new SelectColumns(['id', 'capabilities']),
                'user_login' => $identifier,
            ]);

        $wordpressRoles = $this->interop->getRepository(Option::class)
            ->find($this->tablePrefix . 'user_roles');

        $userRoles = [];
        $userCapabilities = [];

        foreach (array_keys($userInterop->capabilities->data, true) as $capability) {
            if (\array_key_exists($capability, $wordpressRoles)) {
                $userRoles[] = $capability;
                $userCapabilities = [...$userCapabilities, ...array_keys(
                    $wordpressRoles[$capability]['capabilities'],
                    true,
                )];
            } else {
                $userCapabilities[] = $capability;
            }
        }

        $user->setRoles($userRoles);
        $user->setCapabilities($userCapabilities);

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', \get_class($user)));
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class || is_subclass_of($class, User::class);
    }
}
