<?php

namespace App\Security\Voter;

use App\Entity\Ecole;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EcoleVoter extends Voter
{
    const VIEW = 'VIEW';
    
    private Security $security;
    
    public function __construct(Security $security) 
    {
        $this->security = $security;
    }
    
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::VIEW && $subject instanceof Ecole;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // Pour l'instant, comme l'entité PriseDeVue n'existe pas encore,
        // on simplifie les règles d'autorisation:
        
        // Les responsables administratifs et administrateurs peuvent voir toutes les écoles
        if ($this->security->isGranted('ROLE_ADMIN') || $this->security->isGranted('ROLE_RESPONSABLE_ADMINISTRATIF')) {
            return true;
        }
        
        // Pour les photographes, on peut temporairement autoriser la vue de toutes les écoles
        // ou restreindre complètement jusqu'à l'implémentation de PriseDeVue
        if ($this->security->isGranted('ROLE_PHOTOGRAPHE')) {
            // Option 1: Autoriser tous les photographes à voir toutes les écoles
            return true;
            
            // Option 2: Restreindre l'accès jusqu'à l'implémentation de PriseDeVue
            // return false;
        }
        
        return false;
    }
}
