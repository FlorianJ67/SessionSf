<?php

namespace App\Entity;

use App\Repository\ModuleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModuleRepository::class)]
class Module
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'modules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $Categorie = null;

    #[ORM\OneToMany(mappedBy: 'module', targetEntity: ContenuSession::class, orphanRemoval: true)]
    private Collection $contenuSession;

    public function __construct()
    {
        $this->contenuSession = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->Categorie;
    }

    public function setCategorie(?Categorie $Categorie): self
    {
        $this->Categorie = $Categorie;

        return $this;
    }

    /**
     * @return Collection<int, ContenuSession>
     */
    public function getContenuSession(): Collection
    {
        return $this->contenuSession;
    }

    public function addContenuSession(ContenuSession $contenuSession): self
    {
        if (!$this->contenuSession->contains($contenuSession)) {
            $this->contenuSession->add($contenuSession);
            $contenuSession->setModule($this);
        }

        return $this;
    }

    public function removeContenuSession(ContenuSession $contenuSession): self
    {
        if ($this->contenuSession->removeElement($contenuSession)) {
            // set the owning side to null (unless already changed)
            if ($contenuSession->getModule() === $this) {
                $contenuSession->setModule(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

}
