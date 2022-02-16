<?php

namespace App\Entity;

use App\Repository\AthleteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: AthleteRepository::class),
    ORM\HasLifecycleCallbacks
    ]
class Athlete
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 65)]
    private $nom;

    #[ORM\Column(type: 'string', length: 65)]
    private $prenom;

    #[ORM\Column(type: 'date')]
    private $date_naissance;

    #[
        ORM\Column(type: 'string', length: 40),
        Assert\File(mimeTypes:["image/png", "image/jpeg"])
        ]
    private $photo;

    #[ORM\ManyToOne(targetEntity: Discipline::class, inversedBy: 'athletes')]
    #[ORM\JoinColumn(nullable: false)]
    private $discipline;

    #[ORM\ManyToOne(targetEntity: Pays::class, inversedBy: 'athletes')]
    #[ORM\JoinColumn(nullable: false)]
    private $pays;

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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance): self
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getDiscipline(): ?Discipline
    {
        return $this->discipline;
    }

    public function setDiscipline(?Discipline $discipline): self
    {
        $this->discipline = $discipline;

        return $this;
    }

    public function getPays(): ?Pays
    {
        return $this->pays;
    }

    public function setPays(?Pays $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    #[ORM\PostRemove]
    public function removePhoto()
    {
        if (file_exists(__DIR__ ."/../../public/img/upload/profil/" . $this->photo)) {
            unlink(__DIR__ ."/../../public/img/upload/profil/" . $this->photo);
        }
    }
}
