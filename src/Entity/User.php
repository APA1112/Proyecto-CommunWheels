<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column]
    private ?bool $isAdmin = null;

    #[ORM\Column]
    private ?bool $isGroupAdmin = null;

    #[ORM\Column]
    private ?bool $isDriver = null;

    #[ORM\OneToOne(inversedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Driver $driver = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function isIsAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): static
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function isIsGroupAdmin(): ?bool
    {
        return $this->isGroupAdmin;
    }

    public function setIsGroupAdmin(bool $isGroupAdmin): static
    {
        $this->isGroupAdmin = $isGroupAdmin;

        return $this;
    }

    public function isIsDriver(): ?bool
    {
        return $this->isDriver;
    }

    public function setIsDriver(bool $isDriver): static
    {
        $this->isDriver = $isDriver;

        return $this;
    }

    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    public function setDriver(Driver $driver): static
    {
        // set the owning side of the relation if necessary
        if ($driver->getUser() !== $this) {
            $driver->setUser($this);
        }

        $this->driver = $driver;

        return $this;
    }


    public function getRoles(): array
    {
        $roles = [];
        //Guarantee at least every user has ROLE_USER
        $roles[] = 'ROLE_DRIVER';

        if ($this->isGroupAdmin){
            $roles[] = 'ROLE_GROUP_ADMIN';
        }

        if ($this->isAdmin){
            $roles[] = 'ROLE_ADMIN';
        }

        return array_unique($roles);
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
        // In case you store any temporary, sensitive information
    }


    public function getUserIdentifier(): string
    {
        return (string)$this->username;
    }

    public function __toString(): string
    {
        return $this->getUsername();
    }

    private function replaceSpecialCharacter($string): string
    {
        $specialCharacter = array(
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u',
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'ñ' => 'n', 'Ñ' => 'N',
            'ä' => 'a', 'ë' => 'e', 'ï' => 'i', 'ö' => 'o', 'ü' => 'u',
            'Ä' => 'A', 'Ë' => 'E', 'Ï' => 'I', 'Ö' => 'O', 'Ü' => 'U',
            'ç' => 'c', 'Ç' => 'C'
        );
        return strtr($string, $specialCharacter);
    }

    public function generateUsername($name, $lastname): string
    {
        $name = $this->replaceSpecialCharacter($name);
        $lastName = $this->replaceSpecialCharacter($lastname);

        $nameLtrs = substr($name, 0, 3);
        $lastNames = explode(' ', $lastName);
        $firstLastName = substr($lastNames[0], 0, 3);
        $secondLastName = substr($lastNames[1], 0, 3);
        $username = $nameLtrs . '.' . $firstLastName . $secondLastName;

        return $username;
    }
}
