<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'player1', targetEntity: Partie::class)]
    private Collection $Partie;

    #[ORM\OneToMany(mappedBy: 'player2', targetEntity: Partie::class)]
    private Collection $parties;

    #[ORM\Column]
    private ?int $scoreTotal = 0;

    public function __construct()
    {
        $this->Partie = new ArrayCollection();
        $this->parties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
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
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    // /**
    //  * @return Collection<int, Partie>
    //  */
    // public function getPartie(): Collection
    // {
    //     return $this->Partie;
    // }

    // public function addPartie(Partie $partie): self
    // {
    //     if (!$this->Partie->contains($partie)) {
    //         $this->Partie->add($partie);
    //         $partie->setPlayer1($this);
    //     }

    //     return $this;
    // }

    // public function removePartie(Partie $partie): self
    // {
    //     if ($this->Partie->removeElement($partie)) {
    //         // set the owning side to null (unless already changed)
    //         if ($partie->getPlayer1() === $this) {
    //             $partie->setPlayer1(null);
    //         }
    //     }

    //     return $this;
    // }

    /**
     * @return Collection<int, Partie>
     */
    public function getParties(): Collection
    {
        return $this->parties;
    }

    public function addParty(Partie $party): self
    {
        if (!$this->parties->contains($party)) {
            $this->parties->add($party);
            $party->setPlayer2($this);
        }

        return $this;
    }

    public function removeParty(Partie $party): self
    {
        if ($this->parties->removeElement($party)) {
            // set the owning side to null (unless already changed)
            if ($party->getPlayer2() === $this) {
                $party->setPlayer2(null);
            }
        }

        return $this;
    }

    public function getScoreTotal(): ?int
    {
        return $this->scoreTotal;
    }

    public function setScoreTotal(int $scoreTotal): static
    {
        $this->scoreTotal = $scoreTotal;

        return $this;
    }
}
