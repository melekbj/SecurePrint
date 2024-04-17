<?php

namespace App\Entity;

use App\Repository\DeviMaterielRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DeviMaterielRepository::class)]
class DeviMateriel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'deviMateriels')]
    private ?Devi $devi = null;

    #[ORM\ManyToOne(inversedBy: 'deviMateriels')]
    private ?Materiel $materiel = null;

    #[ORM\Column(nullable: true)]
    private ?float $prix = null;

    #[ORM\Column]
    private ?int $qte = null;

    #[ORM\Column(nullable: true, options:["default" => 0])]
    private ?float $tva = null;

    #[ORM\Column(nullable: true, options:["default" => 0])]
    private ?float $remise = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDevi(): ?Devi
    {
        return $this->devi;
    }

    public function setDevi(?Devi $devi): static
    {
        $this->devi = $devi;

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


    public function getQte(): ?int
    {
        return $this->qte;
    }

    public function setQte(int $qte): self
    {
        $this->qte = $qte;

        return $this;
    }

    public function getTva(): ?float
    {
        return $this->tva;
    }

    public function setTva(?float $tva): self
    {
        $this->tva = $tva;

        return $this;
    }

    public function getRemise(): ?float
    {
        return $this->remise;
    }

    public function setRemise(?float $remise): self
    {
        $this->remise = $remise;

        return $this;
    }
}