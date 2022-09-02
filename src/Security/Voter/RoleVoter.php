<?php

namespace Sword\SwordBundle\Security\Voter;

use Sword\SwordBundle\Security\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;

final class RoleVoter implements VoterInterface
{
    public function __construct(private readonly RoleHierarchyInterface $roleHierarchy)
    {
    }

    public function vote(TokenInterface $token, $subject, array $attributes): int
    {
        $result = VoterInterface::ACCESS_ABSTAIN;

        /** @var User $user */
        $user = $token->getUser() instanceof User ? $token->getUser() : null;

        if (!$user instanceof User) {
            return $result;
        }

        foreach ($attributes as $attribute) {
            $result = VoterInterface::ACCESS_DENIED;

            if (in_array($attribute, $this->roleHierarchy->getReachableRoleNames($user->getRoles()), true)) {
                return VoterInterface::ACCESS_GRANTED;
            }
        }

        return $result;
    }
}
