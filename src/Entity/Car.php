<?php

namespace App\Entity;

use App\Repository\CarRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CarRepository::class)
 */
class Car
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     min = 2,
     *     minMessage = "Entrez un type de plus de deux caractères",
     * )
     */
    private $type;

    /**
     * @ORM\Column(type="json")
     */
    private array $datasheet = [];

    /**
     * @ORM\Column(type="float")
     * @Assert\GreaterThan(value=0, message="Le prix fixé est invalide")
     */
    private ?float $amount;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $rent;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $image;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private ?User $idOwner;

    /**
     * @ORM\Column(type="integer")
     * @Assert\GreaterThan(value=0, message="Renseignez une quantité supérieur à 0")
     */
    private ?int $quantity;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDatasheet(): ?array
    {
        return $this->datasheet;
    }

    public function setDatasheet(array $datasheet): self
    {
        $this->datasheet = $datasheet;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getRent(): ?string
    {
        return $this->rent;
    }

    public function setRent(string $rent): self
    {
        $this->rent = $rent;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getIdOwner(): ?User
    {
        return $this->idOwner;
    }

    public function setIdOwner(?User $idOwner): self
    {
        $this->idOwner = $idOwner;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return (string) $this->getId()." - ".$this->getIdOwner()." - ".$this->getType()." - ".$this->getDatasheet()." - ".$this->getAmount()." - ".$this->getRent()." - ".$this->getImage()." - ".$this->getQuantity();
    }
}
