<?php

namespace App\Controller;

use App\Entity\TypePrise;
use App\Form\TypePriseType;
use App\Repository\TypePriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/type/prise')]
#[IsGranted('ROLE_RESPONSABLE_ADMINISTRATIF')]
final class TypePriseController extends AbstractReferentielController
{
    private TypePriseRepository $typePriseRepository;
    
    // Injection du repository dans le constructeur
    public function __construct(TypePriseRepository $typePriseRepository)
    {
        $this->typePriseRepository = $typePriseRepository;
    }
    
    protected function getEntityClass(): string
    {
        return TypePrise::class;
    }
    
    protected function getFormClass(): string
    {
        return TypePriseType::class;
    }
    
    protected function getEntityName(): string
    {
        return 'type de prise';
    }
    
    protected function getRouteName(): string
    {
        return 'app_type_prise';
    }
    
    protected function getTemplateBase(): string
    {
        return 'type_prise';
    }
    
    protected function getEntityVarName(): string
    {
        return 'type_prise';
    }
    
    protected function getRepository()
    {
        return $this->typePriseRepository;
    }
    
    protected function getEntity()
    {
        // Récupérer l'ID depuis la route
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $id = $request->attributes->get('id');
        
        return $this->typePriseRepository->find($id);
    }
    
    #[Route('/', name: 'app_type_prise_index', methods: ['GET'])]
    public function index(): Response
    {
        return parent::index();
    }
    
    #[Route('/new', name: 'app_type_prise_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        return parent::new($request, $entityManager);
    }
    
    #[Route('/{id}', name: 'app_type_prise_show', methods: ['GET'])]
    public function show(): Response
    {
        return parent::show();
    }
    
    #[Route('/{id}/edit', name: 'app_type_prise_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        return parent::edit($request, $entityManager);
    }
    
    #[Route('/{id}', name: 'app_type_prise_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        return parent::delete($request, $entityManager);
    }
}
