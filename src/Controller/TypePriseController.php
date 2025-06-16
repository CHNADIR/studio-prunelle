<?php

namespace App\Controller;

use App\Entity\TypePrise;
use App\Form\TypePriseType;
use App\Repository\TypePriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/type/prise')]
#[IsGranted('ROLE_RESPONSABLE_ADMINISTRATIF')]
final class TypePriseController extends AbstractController
{
    #[Route('/', name: 'app_type_prise_index', methods: ['GET'])]
    public function index(TypePriseRepository $typePriseRepository): Response
    {
        return $this->render('type_prise/index.html.twig', [
            'type_prises' => $typePriseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_type_prise_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $typePrise = new TypePrise();
        $form = $this->createForm(TypePriseType::class, $typePrise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($typePrise);
            $entityManager->flush();
            
            $this->addFlash('success', 'Le type de prise a été créé avec succès.');
            return $this->redirectToRoute('app_type_prise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_prise/new.html.twig', [
            'type_prise' => $typePrise,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_prise_show', methods: ['GET'])]
    public function show(TypePrise $typePrise): Response
    {
        return $this->render('type_prise/show.html.twig', [
            'type_prise' => $typePrise,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_type_prise_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TypePrise $typePrise, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TypePriseType::class, $typePrise);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Le type de prise a été modifié avec succès.');
            return $this->redirectToRoute('app_type_prise_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('type_prise/edit.html.twig', [
            'type_prise' => $typePrise,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_type_prise_delete', methods: ['POST'])]
    public function delete(Request $request, TypePrise $typePrise, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$typePrise->getId(), $request->request->get('_token'))) {
            $entityManager->remove($typePrise);
            $entityManager->flush();
            
            $this->addFlash('success', 'Le type de prise a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_type_prise_index', [], Response::HTTP_SEE_OTHER);
    }
}
