<?php

namespace App\Controller;

use App\Entity\Ecole;
use App\Form\EcoleType;
use App\Form\EcoleSearchType;
use App\Repository\EcoleRepository;
use App\Repository\PriseDeVueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/ecole')]
class EcoleController extends AbstractController
{
    #[Route('/', name: 'app_ecole_index', methods: ['GET'])]
    public function index(Request $request, EcoleRepository $ecoleRepository): Response
    {
        $searchForm = $this->createForm(EcoleSearchType::class);
        $searchForm->handleRequest($request);
        
        $searchTerm = null;
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchCriteria = $searchForm->getData();
            $searchTerm = $searchCriteria['searchTerm'] ?? null;
        }
        
        $ecoles = $ecoleRepository->findBySearchCriteria($searchTerm);
        
        return $this->render('ecole/index.html.twig', [
            'ecoles' => $ecoles,
            'search_form' => $searchForm->createView(),
            'search_term' => $searchTerm,
        ]);
    }

    #[Route('/new', name: 'app_ecole_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_RESPONSABLE_ADMINISTRATIF')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ecole = new Ecole();
        $ecole->setActive(true); // Par défaut, une nouvelle école est active
        
        $form = $this->createForm(EcoleType::class, $ecole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ecole);
            $entityManager->flush();
            
            $this->addFlash('success', 'École ajoutée avec succès.');
            return $this->redirectToRoute('app_ecole_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ecole/new.html.twig', [
            'ecole' => $ecole,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ecole_show', methods: ['GET'])]
    public function show(Ecole $ecole, PriseDeVueRepository $priseDeVueRepository): Response
    {
        // Utilisez le Voter pour vérifier l'accès
        $this->denyAccessUnlessGranted('VIEW', $ecole);
        
        // Récupération des dernières prises de vue
        $latestPriseDeVues = $priseDeVueRepository->findLatestByEcole($ecole, 5);
        
        // Récupération des statistiques
        $stats = $priseDeVueRepository->getStatsByEcole($ecole);
        
        return $this->render('ecole/show.html.twig', [
            'ecole' => $ecole,
            'latestPriseDeVues' => $latestPriseDeVues,
            'stats' => $stats,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_ecole_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_RESPONSABLE_ADMINISTRATIF')]
    public function edit(Request $request, Ecole $ecole, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EcoleType::class, $ecole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'École modifiée avec succès.');
            return $this->redirectToRoute('app_ecole_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ecole/edit.html.twig', [
            'ecole' => $ecole,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_ecole_delete', methods: ['POST'])]
    #[IsGranted('ROLE_RESPONSABLE_ADMINISTRATIF')]
    public function delete(Request $request, Ecole $ecole, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ecole->getId(), $request->request->get('_token'))) {
            // Comme PriseDeVue n'existe pas encore, on supprime directement l'école
            $entityManager->remove($ecole);
            $entityManager->flush();
            
            $this->addFlash('success', 'École supprimée avec succès.');
        }

        return $this->redirectToRoute('app_ecole_index', [], Response::HTTP_SEE_OTHER);
    }
}
