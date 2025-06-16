<?php

namespace App\Entity;

use App\Repository\TypeVenteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// Si vous voulez UniqueEntity, ajoutez :
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity; // Décommentez si vous utilisez UniqueEntity

#[ORM\Entity(repositoryClass: TypeVenteRepository::class)] // Décommentez cette ligne
// #[UniqueEntity(fields: ['nom'], message: 'Ce type de vente existe déjà.')] // Vous pouvez aussi décommenter celle-ci si le nom doit être unique
class TypeVente
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true, unique: true)] // unique: true pour la BDD
    #[Assert\NotBlank(message: "Le nom du type de vente ne peut pas être vide.")]
    #[Assert\Length(max: 100, maxMessage: "Le nom du type de vente ne doit pas dépasser {{ limit }} caractères.")]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? ''; // Assurez-vous que __toString retourne toujours une chaîne
    }
}
