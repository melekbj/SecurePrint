<?php

namespace App\Entity;

use App\Repository\CommandeMaterielRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeMaterielRepository::class)]
class CommandeMateriel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'commandeMateriels')]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(inversedBy: 'commandeMateriels')]
    private ?Materiel $materiel = null;

    #[ORM\Column(nullable: true)]
    private ?float $prix = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): static
    {
        $this->commande = $commande;

        return $this;
    }

    public function getMateriel(): ?Materiel
    {
        return $this->materiel;
    }

    public function setMateriel(?Materiel $materiel): static
    {
        $this->materiel = $materiel;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }
}
