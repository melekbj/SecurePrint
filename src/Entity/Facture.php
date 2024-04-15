<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: FactureRepository::class)]
#[UniqueEntity(fields: ['code'], message: 'There is already a command with this code')]
class Facture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255,unique:true)]
    private ?string $code = null;

    #[ORM\Column(length: 255,unique:false)]
    private ?string $type = null;

    #[ORM\Column(nullable: true, options:["default" => 0])]
    private ?float $remise = null;

    #[ORM\Column(nullable: true, options:["default" => 0])]
    private ?float $timbre = null;


    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'factures')]
    private ?Clients $client = null;

    #[ORM\OneToMany(mappedBy: 'facture', targetEntity: FactureMateriel::class, cascade:['remove'])]
    private Collection $factureMateriels;

    public function __construct()
    {
        $this->factureMateriels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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

    public function getTimbre(): ?float
    {
        return $this->timbre;
    }

    public function setTimbre(?float $timbre): self
    {
        $this->timbre = $timbre;

        return $this;
    }


    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getClient(): ?Clients
    {
        return $this->client;
    }

    public function setClient(?Clients $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection<int, FactureMateriel>
     */
    public function getFactureMateriels(): Collection
    {
        return $this->factureMateriels;
    }

    public function addFactureMateriel(FactureMateriel $factureMateriel): static
    {
        if (!$this->factureMateriels->contains($factureMateriel)) {
            $this->factureMateriels->add($factureMateriel);
            $factureMateriel->setFacture($this);
        }

        return $this;
    }

    public function removeFactureMateriel(FactureMateriel $factureMateriel): static
    {
        if ($this->factureMateriels->removeElement($factureMateriel)) {
            // set the owning side to null (unless already changed)
            if ($factureMateriel->getFacture() === $this) {
                $factureMateriel->setFacture(null);
            }
        }

        return $this;
    }
}
