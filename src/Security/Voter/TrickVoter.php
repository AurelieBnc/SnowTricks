<?php

namespace App\Security\Voter;

use App\Entity\Trick;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TrickVoter extends Voter
{
    public const CREATE = 'TRICK_CREATE';
    public const EDIT = 'TRICK_EDIT';
    public const DELETE = 'TRICK_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::EDIT, self::DELETE])
            && $subject instanceof Trick;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        $userRoles = $user->getRoles();

        switch ($attribute) {
            case self::CREATE:
               return in_array('ROLE_ADMIN', $userRoles);
                break;
            case self::EDIT:
                return in_array('ROLE_ADMIN', $userRoles);
                break;
            case self::DELETE:
               return in_array('ROLE_ADMIN', $userRoles);
                break;
        }

        return false;
    }

}
