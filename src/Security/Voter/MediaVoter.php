<?php

namespace App\Security\Voter;

use App\Entity\Media;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class MediaVoter extends Voter
{
    public const EDIT = 'MEDIA_EDIT';
    public const DELETE = 'MEDIA_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Media;
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
