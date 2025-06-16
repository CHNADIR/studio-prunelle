<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Form\ThemeType;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/theme')]
#[IsGranted('ROLE_RESPONSABLE_ADMINISTRATIF')]
final class ThemeController extends AbstractReferentielController
{
    private ThemeRepository $themeRepository;
    
    // Injection du repository dans le constructeur
    public function __construct(ThemeRepository $themeRepository)
    {
        $this->themeRepository = $themeRepository;
    }
    
    protected function getEntityClass(): string
    {
        return Theme::class;
    }
    
    protected function getFormClass(): string
    {
        return ThemeType::class;
    }
    
    protected function getEntityName(): string
    {
        return 'thème';
    }
    
    protected function getRouteName(): string
    {
        return 'app_theme';
    }
    
    protected function getTemplateBase(): string
    {
        return 'theme';
    }
    
    protected function getEntityVarName(): string
    {
        return 'theme';
    }
    
    protected function getRepository()
    {
        return $this->themeRepository;
    }
    
    protected function getEntity()
    {
        // Récupérer l'ID depuis la route
        $request = $this->container->get('request_stack')->getCurrentRequest();
        $id = $request->attributes->get('id');
        
        return $this->themeRepository->find($id);
    }
    
    #[Route('/', name: 'app_theme_index', methods: ['GET'])]
    public function index(): Response
    {
        return parent::index();
    }
    
    #[Route('/new', name: 'app_theme_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        return parent::new($request, $entityManager);
    }
    
    #[Route('/{id}', name: 'app_theme_show', methods: ['GET'])]
    public function show(): Response
    {
        return parent::show();
    }
    
    #[Route('/{id}/edit', name: 'app_theme_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        return parent::edit($request, $entityManager);
    }
    
    #[Route('/{id}', name: 'app_theme_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        return parent::delete($request, $entityManager);
    }
}
