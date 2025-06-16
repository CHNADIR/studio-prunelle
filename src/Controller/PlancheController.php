<?php

namespace App\Controller;

use App\Entity\Planche;
use App\Form\PlancheType;
use App\Repository\PlancheRepository;
use App\Security\Voter\PlancheVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/planche')]
#[IsGranted('ROLE_RESPONSABLE_ADMINISTRATIF')]
final class PlancheController extends AbstractController
{
    #[Route('/', name: 'app_planche_index', methods: ['GET'])]
    public function index(PlancheRepository $plancheRepository): Response
    {
        return $this->render('planche/index.html.twig', [
            'planches' => $plancheRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_planche_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted(PlancheVoter::CREATE, null);
        
        $planche = new Planche();
        $form = $this->createForm(PlancheType::class, $planche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload d'image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('planches_images_directory'),
                        $newFilename
                    );
                    $planche->setImageFilename($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image');
                }
            }

            $entityManager->persist($planche);
            $entityManager->flush();
            
            $this->addFlash('success', 'La planche a été créée avec succès.');
            return $this->redirectToRoute('app_planche_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('planche/new.html.twig', [
            'planche' => $planche,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_planche_show', methods: ['GET'])]
    public function show(Planche $planche): Response
    {
        $this->denyAccessUnlessGranted(PlancheVoter::VIEW, $planche);
        
        return $this->render('planche/show.html.twig', [
            'planche' => $planche,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_planche_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Planche $planche, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted(PlancheVoter::EDIT, $planche);
        
        $form = $this->createForm(PlancheType::class, $planche);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload d'image
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('planches_images_directory'),
                        $newFilename
                    );
                    
                    // Supprimer l'ancienne image si elle existe
                    if ($planche->getImageFilename()) {
                        $oldImagePath = $this->getParameter('planches_images_directory').'/'.$planche->getImageFilename();
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    
                    $planche->setImageFilename($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image');
                }
            }
            
            $entityManager->flush();
            
            $this->addFlash('success', 'La planche a été modifiée avec succès.');
            return $this->redirectToRoute('app_planche_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('planche/edit.html.twig', [
            'planche' => $planche,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_planche_delete', methods: ['POST'])]
    public function delete(Request $request, Planche $planche, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(PlancheVoter::DELETE, $planche);
        
        if ($this->isCsrfTokenValid('delete'.$planche->getId(), $request->request->get('_token'))) {
            // Supprimer l'image associée
            if ($planche->getImageFilename()) {
                $imagePath = $this->getParameter('planches_images_directory').'/'.$planche->getImageFilename();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            $entityManager->remove($planche);
            $entityManager->flush();
            
            $this->addFlash('success', 'La planche a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_planche_index', [], Response::HTTP_SEE_OTHER);
    }
}
