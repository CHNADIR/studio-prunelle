<?php

namespace App\Controller;

use App\Entity\TypeVente;
use App\Form\TypeVenteType;
use App\Repository\TypeVenteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/type/vente')]
#[IsGranted('ROLE_RESPONSABLE_ADMINISTRATIF')]
final class TypeVenteController extends AbstractReferentielController
{
    private TypeVenteRepository $typeVenteRepository;
    
    // Injection du repository dans le constructeur
    public function __construct(TypeVenteRepository $typeVenteRepository)
    {
        $this->typeVenteRepository = $typeVenteRepository;
    }
    
    protected function getEntityClass(): string
    {
        return TypeVente::class;
    }
    
    protected function getFormClass(): string
    {
        return TypeVenteType::class;
    }
    
    protected function getEntityName(): string
    {
        return 'type de vente';
    }
    
    protected function getRouteName(): string
    {
        return 'app_type_vente';
    }
    
    protected function getTemplateBase(): string
    {
        return 'type_vente';
    }
    
    protected function getEntityVarName(): string
    {
        return 'type_vente';
    }
    
    protected function getRepository()
    {
        return $this->typeVenteRepository;
    }
    
    protected function getEntity()
    {
        // Récupérer l'ID depuis la route
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $id = $request->attributes->get('id');
        
        return $this->typeVenteRepository->find($id);
    }
    
    #[Route('/', name: 'app_type_vente_index', methods: ['GET'])]
    public function index(): Response
    {
        return parent::index();
    }
    
    #[Route('/new', name: 'app_type_vente_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        return parent::new($request, $entityManager);
    }
    
    #[Route('/{id}', name: 'app_type_vente_show', methods: ['GET'])]
    public function show(): Response
    {
        return parent::show();
    }
    
    #[Route('/{id}/edit', name: 'app_type_vente_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        return parent::edit($request, $entityManager);
    }
    
    #[Route('/{id}', name: 'app_type_vente_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        return parent::delete($request, $entityManager);
    }
}
