<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Psr\Log\LoggerInterface;

class UploaderHelper
{
    // Chemins de stockage constants
    public const PLANCHE_IMAGE_PATH = 'planches';
    
    private string $publicUploadsPath;
    private string $privateUploadsPath;
    private SluggerInterface $slugger;
    private Filesystem $filesystem;
    private LoggerInterface $logger;

    public function __construct(
        string $publicUploadsPath,
        string $privateUploadsPath,
        SluggerInterface $slugger,
        Filesystem $filesystem,
        LoggerInterface $logger
    ) {
        $this->publicUploadsPath = $publicUploadsPath;
        $this->privateUploadsPath = $privateUploadsPath;
        $this->slugger = $slugger;
        $this->filesystem = $filesystem;
        $this->logger = $logger;
    }

    /**
     * Upload une image de planche et retourne le nom de fichier
     */
    public function uploadPlancheImage(UploadedFile $uploadedFile, ?string $existingFilename = null): string
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

        // S'assurer que le répertoire existe
        $targetDir = $this->getPlancheDirectory();
        if (!$this->filesystem->exists($targetDir)) {
            $this->filesystem->mkdir($targetDir, 0755);
        }

        try {
            $uploadedFile->move($targetDir, $newFilename);
            
            // Si un ancien fichier existe, on le supprime
            if ($existingFilename) {
                $this->removePlancheImage($existingFilename);
            }
            
            $this->logger->info('Image uploaded successfully', [
                'original_name' => $originalFilename,
                'new_name' => $newFilename,
                'directory' => $targetDir
            ]);
            
            return $newFilename;
            
        } catch (FileException $e) {
            $this->logger->error('Failed to upload image', [
                'exception' => $e->getMessage(),
                'original_name' => $originalFilename
            ]);
            
            throw new \RuntimeException('Une erreur est survenue lors de l\'upload de l\'image.');
        }
    }

    /**
     * Supprime une image de planche
     */
    public function removePlancheImage(?string $filename): void
    {
        if (!$filename) {
            return;
        }

        $filepath = $this->getPlancheDirectory() . '/' . $filename;
        if ($this->filesystem->exists($filepath)) {
            try {
                $this->filesystem->remove($filepath);
                $this->logger->info('Image removed successfully', ['filename' => $filename]);
            } catch (\Exception $e) {
                $this->logger->error('Failed to remove image', [
                    'exception' => $e->getMessage(),
                    'filename' => $filename
                ]);
            }
        }
    }

    /**
     * Retourne le chemin du répertoire pour les images de planches
     */
    public function getPlancheDirectory(): string 
    {
        return $this->publicUploadsPath . '/' . self::PLANCHE_IMAGE_PATH;
    }

    /**
     * Retourne le chemin public pour accéder à une image de planche
     */
    public function getPlancheImagePath(string $filename): string
    {
        return '/uploads/' . self::PLANCHE_IMAGE_PATH . '/' . $filename;
    }
    
    /**
     * Retourne le chemin complet vers un fichier privé
     */
    public function getPrivateFilePath(string $path): string
    {
        return $this->privateUploadsPath . '/' . $path;
    }
}