<?php

namespace App\Security\Voter;

use App\Entity\PriseDeVue;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class PriseDeVueVoter extends Voter
{
    public const VIEW = 'VIEW';
    public const EDIT = 'EDIT';
    public const EDIT_COMMENT = 'EDIT_COMMENT';
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
        if (!in_array($attribute, [self::VIEW, self::EDIT, self::EDIT_COMMENT, self::DELETE, self::CREATE])) {
            return false;
        }

        // only vote on PriseDeVue objects or null for CREATE
        if ($attribute === self::CREATE) {
            return true;
        }

        return $subject instanceof PriseDeVue;
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

        // ROLE_PHOTOGRAPHE a des droits limités
        if ($this->security->isGranted('ROLE_PHOTOGRAPHE')) {
            /** @var PriseDeVue $priseDeVue */
            $priseDeVue = $subject;
            
            // Vérifier si l'utilisateur est une instance de notre entité User
            if (!$user instanceof User) {
                return false;
            }
            
            // Le photographe peut voir et modifier uniquement ses propres prises de vue
            $isOwnPriseDeVue = $priseDeVue->getPhotographe() && 
                               $priseDeVue->getPhotographe()->getId() === $user->getId();
            
            switch ($attribute) {
                case self::VIEW:
                    return $isOwnPriseDeVue;
                
                case self::EDIT_COMMENT:
                    return $isOwnPriseDeVue;
                
                case self::EDIT:
                case self::DELETE:
                case self::CREATE:
                    return false;
            }
        }

        // par défaut, refuser l'accès
        return false;
    }
}
