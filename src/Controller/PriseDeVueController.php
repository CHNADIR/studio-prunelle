<?php

namespace App\Controller;

use App\Entity\PriseDeVue;
use App\Form\PriseDeVueForm;
use App\Repository\PriseDeVueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/prise/de/vue')]
final class PriseDeVueController extends AbstractController
{
    #[Route(name: 'app_prise_de_vue_index', methods: ['GET'])]
    public function index(PriseDeVueRepository $priseDeVueRepository): Response
    {
        return $this->render('prise_de_vue/index.html.twig', [
            'prise_de_vues' => $priseDeVueRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_prise_de_vue_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $priseDeVue = new PriseDeVue();
        $form = $this->createForm(PriseDeVueForm::class, $priseDeVue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($priseDeVue);
            $entityManager->flush();

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
        return $this->render('prise_de_vue/show.html.twig', [
            'prise_de_vue' => $priseDeVue,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_prise_de_vue_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PriseDeVue $priseDeVue, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PriseDeVueForm::class, $priseDeVue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_prise_de_vue_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prise_de_vue/edit.html.twig', [
            'prise_de_vue' => $priseDeVue,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prise_de_vue_delete', methods: ['POST'])]
    public function delete(Request $request, PriseDeVue $priseDeVue, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$priseDeVue->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($priseDeVue);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_prise_de_vue_index', [], Response::HTTP_SEE_OTHER);
    }
}
