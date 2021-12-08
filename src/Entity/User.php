<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *     fields={"email"},
 *     message="L'email est déjà utilisé !"
 * )
 * @method string getUserIdentifier()
 */
class User implements UserInterface, \Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface
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
     *     min = 3,
     *     max = 50,
     *     minMessage = "Votre nom doit contenir au moins 3 caractères",
     *     maxMessage = "Votre nom doit contenir au plus 50 caractères"
     * )
     * @Assert\Regex(pattern="/([A-ZÀ-ÿ][a-z]*)+[ ]([A-ZÀ-ÿ][a-z]*)/", message="Veuillez entrer un Nom/Prénom valident !")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="Entre une adresse mail valide !")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&^_-]{8,}$/", message="Votre mot de passe doit
    contenir 1 lettre, 1 chiffre, pas d'espace et minimum 8 caractères !")
     * @Assert\NotCompromisedPassword(message="Votre mot de passe est sensible à une attaque par force brute !")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Vous n'avez pas tapé le même mot de passe")
     */
    private $confirm_password;

    /**
     * @return mixed
     */
    public function getConfirmPassword()
    {
        return $this->confirm_password;
    }

    /**
     * @param mixed $confirm_password
     */
    public function setConfirmPassword($confirm_password): void
    {
        $this->confirm_password = $confirm_password;
    }

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $role;

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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->getName();
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {

    }

    public function getUsername()
    {
        return $this->getName();
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method string getUserIdentifier()
    }
}