<?php

namespace App\Security\Voter;

use App\Entity\Trick;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class HeaderPictureNameVoter extends Voter
{
    public const EDIT = 'HEADER_PICTURE_NAME_EDIT';
    public const DELETE = 'HEADER_PICTURE_NAME_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Trick;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        $userRoles = $user->getRoles();

        return $attribute === self::EDIT || $attribute === self::DELETE
        ? in_array('ROLE_ADMIN', $userRoles)
        : false;
    }
}
