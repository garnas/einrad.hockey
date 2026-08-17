<?php

namespace App\Entity\Team;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: "ligaleitung",
    uniqueConstraints: [new ORM\UniqueConstraint(name: "login", columns: ["login"])],
    indexes: [new ORM\Index(name: "spieler_id", columns: ["spieler_id"])],
)]
class Ligaleitung
{
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Id]
    #[ORM\Column(name: "ligaleitung_id", type: "integer", nullable: false)]
    private int $ligaleitungId;

    #[ORM\Column(name: "funktion", type: "string", length: 255, nullable: false)]
    private string $funktion;

    #[ORM\Column(name: "email", type: "string", length: 255, nullable: true)]
    private ?string $email;

    #[ORM\Column(name: "login", type: "string", length: 255, nullable: true)]
    private ?string $login;

    #[ORM\Column(name: "passwort", type: "string", length: 255, nullable: true)]
    private ?string $passwort;

    #[ORM\ManyToOne(targetEntity: Spieler::class)]
    #[ORM\JoinColumn(name: "spieler_id", referencedColumnName: "spieler_id")]
    private Spieler $spieler;

    public function getLigaleitungId(): ?int
    {
        return $this->ligaleitungId;
    }

    public function getFunktion(): string
    {
        return $this->funktion;
    }

    public function setFunktion(string $funktion): self
    {
        $this->funktion = $funktion;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(?string $login): self
    {
        $this->login = $login;
        return $this;
    }

    public function getPasswort(): ?string
    {
        return $this->passwort;
    }

    public function setPasswort(?string $passwort): self
    {
        $this->passwort = $passwort;
        return $this;
    }

    public function getSpieler(): Spieler
    {
        return $this->spieler;
    }

    public function setSpieler(Spieler $spieler): self
    {
        $this->spieler = $spieler;
        return $this;
    }

}
