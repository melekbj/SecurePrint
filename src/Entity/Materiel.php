<?php

namespace App\Entity;

use App\Repository\MaterielRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;



#[ORM\Entity(repositoryClass: MaterielRepository::class)]
#[UniqueEntity(fields: ['reference'], message: 'There is already a material with this reference')]
#[Vich\Uploadable]
class Materiel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255,nullable:false)]
    private ?string $nom = null;

    #[Vich\UploadableField(mapping: 'materiels', fileNameProperty: 'photo')]
    public ?File $imageFile = null;

    #[ORM\Column(length: 255, nullable:true)]
    private ?string $photo = null;

    #[ORM\Column(length: 255,unique:true,nullable:true)]
    private ?string $reference = null;

    #[ORM\OneToMany(mappedBy: 'materiel', targetEntity: DeviMateriel::class)]
    private Collection $deviMateriels;

    #[ORM\OneToMany(mappedBy: 'materiel', targetEntity: FactureMateriel::class)]
    private Collection $factureMateriels;
    

    public function __construct()
    {
        $this->deviMateriels = new ArrayCollection();
        $this->factureMateriels = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

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
            $deviMateriel->setMateriel($this);
        }

        return $this;
    }

    public function removeDeviMateriel(DeviMateriel $deviMateriel): static
    {
        if ($this->deviMateriels->removeElement($deviMateriel)) {
            // set the owning side to null (unless already changed)
            if ($deviMateriel->getMateriel() === $this) {
                $deviMateriel->setMateriel(null);
            }
        }

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
            $factureMateriel->setMateriel($this);
        }

        return $this;
    }

    public function removeFactureMateriel(FactureMateriel $factureMateriel): static
    {
        if ($this->factureMateriels->removeElement($factureMateriel)) {
            // set the owning side to null (unless already changed)
            if ($factureMateriel->getMateriel() === $this) {
                $factureMateriel->setMateriel(null);
            }
        }

        return $this;
    }


}
