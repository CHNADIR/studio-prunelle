<?php

namespace App\Entity;

use App\Repository\TypePriseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
// Si vous voulez UniqueEntity, ajoutez :
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity; // Décommentez si vous utilisez UniqueEntity

#[ORM\Entity(repositoryClass: TypePriseRepository::class)] // Décommentez cette ligne
// #[UniqueEntity(fields: ['nom'], message: 'Ce type de prise existe déjà.')] // Vous pouvez aussi décommenter celle-ci si le nom doit être unique
class TypePrise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true, unique: true)] // unique: true pour la BDD
    #[Assert\NotBlank(message: "Le nom du type de prise ne peut pas être vide.")]
    #[Assert\Length(max: 100, maxMessage: "Le nom du type de prise ne doit pas dépasser {{ limit }} caractères.")]
    private ?string $name = null;

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
