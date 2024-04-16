<?php

namespace App\Entity;

use App\Repository\DeviRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: DeviRepository::class)]
#[UniqueEntity(fields: ['code'], message: 'There is already a command with this code')]
class Devi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255,unique:true)]
    private ?string $code = null;

    #[ORM\Column(nullable: true, options:["default" => 0])]
    private ?float $remise = null;

    #[ORM\Column(nullable: true, options:["default" => 0])]
    private ?float $timbre = null;


    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\ManyToOne(inversedBy: 'devis')]
    private ?Clients $client = null;

    #[ORM\OneToMany(mappedBy: 'devi', targetEntity: DeviMateriel::class, cascade:['remove'])]
    private Collection $deviMateriels;

    public function __construct()
    {
        $this->deviMateriels = new ArrayCollection();
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
     * @return Collection<int, DeviMateriel>
     */
    public function getDeviMateriels(): Collection
    {
        return $this->deviMateriels;
    }

    public function addDeviMateriel(DeviMateriel $deviMateriel): static
    {
        if (!$this->deviMateriels->contains($deviMateriel)) {
            $this->deviMateriels->add($deviMateriel);
            $deviMateriel->setDevi($this);
        }

        return $this;
    }

    public function removeDeviMateriel(DeviMateriel $deviMateriel): static
    {
        if ($this->deviMateriels->removeElement($deviMateriel)) {
            // set the owning side to null (unless already changed)
            if ($deviMateriel->getDevi() === $this) {
                $deviMateriel->setDevi(null);
            }
        }

        return $this;
    }
}
