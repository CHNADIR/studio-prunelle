<?php

namespace App\Controller;

use App\Service\UploaderHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/secure-files')]
class SecureImageController extends AbstractController
{
    public function __construct(
        private UploaderHelper $uploaderHelper
    ) {
    }

    /**
     * Sert une image de planche stockée en privé.
     * Un Voter plus spécifique pourrait être ajouté ici si l'accès dépend
     * de droits plus fins sur l'entité Planche elle-même.
     */
    #[Route('/planche/{filename}', name: 'app_secure_planche_image')]
    #[IsGranted('ROLE_USER')] // Au minimum, l'utilisateur doit être connecté. Adaptez au besoin.
    public function plancheImage(string $filename): Response
    {
        $filePath = $this->uploaderHelper->getPrivateFilePath('planches/' . $filename);

        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new NotFoundHttpException('Image non trouvée ou non accessible.');
        }

        $response = new BinaryFileResponse($filePath);
        // Pour l'affichage dans le navigateur (si le type MIME est géré)
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $filename);
        // Pour forcer le téléchargement :
        // $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        
        // Il est recommandé de ne pas mettre en cache les réponses de fichiers privés par défaut
        $response->headers->set('Cache-Control', 'private, no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}