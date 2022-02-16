<?php

namespace App\Entity;

use App\Repository\PaysRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PaysRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Pays
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $nom;

    #[
        ORM\Column(type: 'string', length: 10),
        Assert\File(mimeTypes: ['image/png'])
    ]
    private $drapeau;

    #[ORM\OneToMany(mappedBy: 'pays', targetEntity: Athlete::class)]
    private $athletes;

    public function __construct()
    {
        $this->athletes = new ArrayCollection();
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

    public function getDrapeau()
    {
        return $this->drapeau;
    }

    public function setDrapeau($drapeau): self
    {
        $this->drapeau = $drapeau;

        return $this;
    }

    /**
     * @return Collection|Athlete[]
     */
    public function getAthletes(): Collection
    {
        return $this->athletes;
    }

    public function addAthlete(Athlete $athlete): self
    {
        if (!$this->athletes->contains($athlete)) {
            $this->athletes[] = $athlete;
            $athlete->setPays($this);
        }

        return $this;
    }

    public function removeAthlete(Athlete $athlete): self
    {
        if ($this->athletes->removeElement($athlete)) {
            // set the owning side to null (unless already changed)
            if ($athlete->getPays() === $this) {
                $athlete->setPays(null);
            }
        }

        return $this;
    }

    #[ORM\PostRemove]
    public function removeDrapeau ()
    {
        if (file_exists(__DIR__ ."/../../public/img/upload/drapeau/". $this->drapeau)) {
            unlink(__DIR__ ."/../../public/img/upload/drapeau/". $this->drapeau);
        }
    }
}
