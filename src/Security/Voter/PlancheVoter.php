<?php

namespace App\Security\Voter;

use App\Entity\Planche;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PlancheVoter extends Voter
{
    public const VIEW = 'VIEW';
    public const EDIT = 'EDIT';
    public const DELETE = 'DELETE';
    public const CREATE = 'CREATE';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::DELETE, self::CREATE])) {
            return false;
        }

        // only vote on Planche objects or null for CREATE
        if ($attribute === self::CREATE) {
            return true;
        }

        return $subject instanceof Planche;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ROLE_ADMIN et ROLE_RESPONSABLE_ADMINISTRATIF ont tous les droits
        if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_RESPONSABLE_ADMINISTRATIF')) {
            return true;
        }

        // ROLE_PHOTOGRAPHE peut uniquement voir les planches
        if ($this->security->isGranted('ROLE_PHOTOGRAPHE')) {
            return $attribute === self::VIEW;
        }

        // par défaut, refuser l'accès
        return false;
    }
}