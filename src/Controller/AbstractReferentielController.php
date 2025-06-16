<?php
// filepath: /home/nch/projects/studio-prunelle/src/Controller/AbstractReferentielController.php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_RESPONSABLE_ADMINISTRATIF')]
abstract class AbstractReferentielController extends AbstractController
{
    abstract protected function getEntityClass(): string;
    abstract protected function getFormClass(): string;
    abstract protected function getEntityName(): string;
    abstract protected function getRouteName(): string;
    abstract protected function getTemplateBase(): string;
    abstract protected function getEntityVarName(): string;

    public function index(): Response
    {
        $repository = $this->getRepository();
        $items = $repository->findAll();
        
        return $this->render($this->getTemplateBase() . '/index.html.twig', [
            $this->getEntityVarName() . 's' => $items,
        ]);
    }

    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entityClass = $this->getEntityClass();
        $entity = new $entityClass();
        $form = $this->createForm($this->getFormClass(), $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($entity);
            $entityManager->flush();
            
            $this->addFlash('success', 'Le ' . $this->getEntityName() . ' a été créé avec succès.');
            return $this->redirectToRoute($this->getRouteName() . '_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render($this->getTemplateBase() . '/new.html.twig', [
            $this->getEntityVarName() => $entity,
            'form' => $form,
        ]);
    }

    public function show(): Response
    {
        $entity = $this->getEntity();
        
        return $this->render($this->getTemplateBase() . '/show.html.twig', [
            $this->getEntityVarName() => $entity,
        ]);
    }

    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->getEntity();
        $form = $this->createForm($this->getFormClass(), $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            $this->addFlash('success', 'Le ' . $this->getEntityName() . ' a été modifié avec succès.');
            return $this->redirectToRoute($this->getRouteName() . '_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render($this->getTemplateBase() . '/edit.html.twig', [
            $this->getEntityVarName() => $entity,
            'form' => $form,
        ]);
    }

    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        $entity = $this->getEntity();
        
        if ($this->isCsrfTokenValid('delete'.$entity->getId(), $request->request->get('_token'))) {
            $entityManager->remove($entity);
            $entityManager->flush();
            
            $this->addFlash('success', 'Le ' . $this->getEntityName() . ' a été supprimé avec succès.');
        }

        return $this->redirectToRoute($this->getRouteName() . '_index', [], Response::HTTP_SEE_OTHER);
    }
    
    // Méthode à implémenter par les classes filles pour récupérer l'entité
    abstract protected function getEntity();
    
    // Méthode à implémenter par les classes filles pour récupérer le repository
    abstract protected function getRepository();
}