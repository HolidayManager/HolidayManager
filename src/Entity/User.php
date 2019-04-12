<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Assert\NotNull()
     * @Assert\Length(min="4",max="180")
     * @Assert\Regex(pattern="/^[A-Za-z0-9]+(?:[ _-][A-Za-z0-9]+)*$/")
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     * @Assert\NotNull()
     * @Assert\NotBlank()
     * @Assert\Regex(pattern="/^[A-Z]+_[A-Z]+$/")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Regex(pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/")
     * @Assert\Length(min="8")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/^[\w'\-,.][^0-9_!¡?÷?¿/\\+=@#$%ˆ&*(){}|~<>;:[\]]{2,}$/")
     * @Assert\Length(min="2",max="200")
     *@Assert\NotBlank()
     * @Assert\NotNull()
     *
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(pattern="/^[\w'\-,.][^0-9_!¡?÷?¿/\\+=@#$%ˆ&*(){}|~<>;:[\]]{2,}$/")
     * @Assert\Length(min="2",max="200")
     * @Assert\NotBlank()
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     * @Assert\NotBlank()
     * @Assert\NotNull()
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date()
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $birthDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date()
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $startDate;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotNull()
     *
     */
    private $holidayLeft;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="guid")
     */
    private $activationToken;

    /**
     * @ORM\Column(type="date")
     * @Assert\Date()
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $referenceYear;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Department", inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     */
    private $department;


    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

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

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getHolidayLeft(): ?int
    {
        return $this->holidayLeft;
    }

    public function setHolidayLeft(int $holidayLeft): self
    {
        $this->holidayLeft = $holidayLeft;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getActivationToken(): ?string
    {
        return $this->activationToken;
    }

    public function setActivationToken(string $activationToken): self
    {
        $this->activationToken = $activationToken;

        return $this;
    }

    public function getReferenceYear(): ?\DateTimeInterface
    {
        return $this->referenceYear;
    }

    public function setReferenceYear(\DateTimeInterface $referenceYear): self
    {
        $this->referenceYear = $referenceYear;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
    }
}
