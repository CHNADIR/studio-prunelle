<?php

namespace App\Controller;

use App\Entity\PriseDeVue;
use App\Form\PriseDeVueForm;
use App\Repository\PriseDeVueRepository;
use App\Security\Voter\PriseDeVueVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/prise/de/vue')]
final class PriseDeVueController extends AbstractController
{
    #[Route('/', name: 'app_prise_de_vue_index', methods: ['GET'])]
    public function index(PriseDeVueRepository $priseDeVueRepository): Response
    {
        // Si c'est un photographe, montrer uniquement ses prises de vue
        if ($this->isGranted('ROLE_PHOTOGRAPHE') && 
            !$this->isGranted('ROLE_ADMIN') && 
            !$this->isGranted('ROLE_RESPONSABLE_ADMINISTRATIF')) {
            $prisesDeVue = $priseDeVueRepository->findBy(['photographe' => $this->getUser()]);
            $title = 'Mon planning';
        } else {
            $prisesDeVue = $priseDeVueRepository->findAll();
            $title = 'Liste des prises de vue';
        }
        
        return $this->render('prise_de_vue/index.html.twig', [
            'prise_de_vues' => $prisesDeVue,
            'title' => $title
        ]);
    }

    #[Route('/new', name: 'app_prise_de_vue_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur a le droit de créer une prise de vue
        $this->denyAccessUnlessGranted(PriseDeVueVoter::CREATE, null);
        
        $priseDeVue = new PriseDeVue();
        $form = $this->createForm(PriseDeVueForm::class, $priseDeVue, [
            'can_edit_full' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($priseDeVue);
            $entityManager->flush();
            
            $this->addFlash('success', 'La prise de vue a été créée avec succès.');
            return $this->redirectToRoute('app_prise_de_vue_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prise_de_vue/new.html.twig', [
            'prise_de_vue' => $priseDeVue,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prise_de_vue_show', methods: ['GET'])]
    public function show(PriseDeVue $priseDeVue): Response
    {
        // Vérifier que l'utilisateur a le droit de voir cette prise de vue
        $this->denyAccessUnlessGranted(PriseDeVueVoter::VIEW, $priseDeVue);
        
        return $this->render('prise_de_vue/show.html.twig', [
            'prise_de_vue' => $priseDeVue,
            'can_edit_full' => $this->isGranted(PriseDeVueVoter::EDIT, $priseDeVue),
            'can_edit_comment' => $this->isGranted(PriseDeVueVoter::EDIT_COMMENT, $priseDeVue),
            'can_delete' => $this->isGranted(PriseDeVueVoter::DELETE, $priseDeVue),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_prise_de_vue_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PriseDeVue $priseDeVue, EntityManagerInterface $entityManager): Response
    {
        // Un photographe peut éditer seulement le commentaire de ses propres prises de vue
        $canEditFull = $this->isGranted(PriseDeVueVoter::EDIT, $priseDeVue);
        $canEditComment = $this->isGranted(PriseDeVueVoter::EDIT_COMMENT, $priseDeVue);
        
        if (!$canEditFull && !$canEditComment) {
            throw $this->createAccessDeniedException('Vous n\'avez pas le droit de modifier cette prise de vue.');
        }
        
        $form = $this->createForm(PriseDeVueForm::class, $priseDeVue, [
            'can_edit_full' => $canEditFull,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'La prise de vue a été modifiée avec succès.');
            return $this->redirectToRoute('app_prise_de_vue_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prise_de_vue/edit.html.twig', [
            'prise_de_vue' => $priseDeVue,
            'form' => $form,
            'can_edit_full' => $canEditFull,
        ]);
    }

    #[Route('/{id}', name: 'app_prise_de_vue_delete', methods: ['POST'])]
    public function delete(Request $request, PriseDeVue $priseDeVue, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'utilisateur a le droit de supprimer cette prise de vue
        $this->denyAccessUnlessGranted(PriseDeVueVoter::DELETE, $priseDeVue);
        
        if ($this->isCsrfTokenValid('delete'.$priseDeVue->getId(), $request->request->get('_token'))) {
            $entityManager->remove($priseDeVue);
            $entityManager->flush();
            
            $this->addFlash('success', 'La prise de vue a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_prise_de_vue_index', [], Response::HTTP_SEE_OTHER);
    }
}
