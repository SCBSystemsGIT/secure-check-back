<?php

namespace App\Entity;

use App\Repository\QRUserRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: QRUserRepository::class)]
class QRUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $uidn = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(length: 255)]
    private ?string $dateExp = null;

    #[ORM\Column(length: 255)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255)]
    private ?string $lastname = null;

    #[ORM\Column(length: 255)]
    private ?string $contact = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstname;
    }

    public function setFirstName(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastname;
    }

    public function setLastName(string $lastname): static
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getUidn(): ?string
    {
        return $this->uidn;
    }

    public function setUidn(string $uidn): static
    {
        $this->uidn = $uidn;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDateExp(): ?string
    {
        return $this->dateExp;
    }

    public function setDateExp(string $dateExp): static
    {
        $this->dateExp = $dateExp;

        return $this;
    }
}
