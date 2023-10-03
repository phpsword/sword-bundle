<?php

namespace Sword\SwordBundle\Security;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Sword\SwordBundle\Entity\WordpressEntityInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
#[ORM\Table('users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, WordpressEntityInterface, EquatableInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'ID', type: 'bigint', options: [
        'unsigned' => true
    ])]
    protected ?int $id = null;

    #[ORM\Column(name: 'user_login', type: 'string', length: 60)]
    protected ?string $login = null;

    #[ORM\Column(name: 'user_pass', type: 'string', length: 255)]
    protected ?string $pass = null;

    #[ORM\Column(name: 'user_nicename', type: 'string', length: 50)]
    protected ?string $nicename = null;

    #[ORM\Column(name: 'user_email', type: 'string', length: 100)]
    protected ?string $email = null;

    #[ORM\Column(name: 'user_url', type: 'string', length: 100)]
    protected ?string $url = null;

    #[ORM\Column(name: 'user_registered', type: 'datetime')]
    protected ?DateTime $registered;

    #[ORM\Column(name: 'user_activation_key', type: 'string', length: 255)]
    protected ?string $activationKey = null;

    #[ORM\Column(name: 'user_status', type: 'integer')]
    protected ?int $status = null;

    #[ORM\Column(name: 'display_name', type: 'string', length: 250)]
    protected ?string $displayName = null;

    protected ArrayCollection $metas;

    private array $roles = [];

    private array $capabilities = [];

    public function __construct()
    {
        $this->registered = new DateTime('1970-01-01 00:00:00');
        $this->metas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPass(): ?string
    {
        return $this->pass;
    }

    public function setPass(?string $pass): self
    {
        $this->pass = $pass;

        return $this;
    }

    public function getNicename(): ?string
    {
        return $this->nicename;
    }

    public function setNicename(?string $nicename): self
    {
        $this->nicename = $nicename;

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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getRegistered(): ?DateTime
    {
        return $this->registered;
    }

    public function setRegistered(?DateTime $registered): self
    {
        $this->registered = $registered;

        return $this;
    }

    public function getActivationKey(): ?string
    {
        return $this->activationKey;
    }

    public function setActivationKey(?string $activationKey): self
    {
        $this->activationKey = $activationKey;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(?int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getMetas(): ArrayCollection
    {
        return $this->metas;
    }

    public function setMetas(ArrayCollection $metas): self
    {
        $this->metas = $metas;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getCapabilities(): array
    {
        $capabilities = $this->capabilities;

        return array_unique($capabilities);
    }

    public function setCapabilities(array $capabilities): self
    {
        $this->capabilities = $capabilities;

        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getPassword(): ?string
    {
        return $this->getPass();
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if ($user instanceof self) {
            $isEqual =
                \count($this->getRoles()) === \count($user->getRoles())
                && \count($this->getCapabilities()) === \count($user->getCapabilities())
            ;

            if ($isEqual) {
                foreach ($this->getRoles() as $role) {
                    $isEqual = $isEqual && \in_array($role, $user->getRoles(), true);
                }

                foreach ($this->getCapabilities() as $capability) {
                    $isEqual = $isEqual && \in_array($capability, $user->getCapabilities(), true);
                }
            }

            return $isEqual;
        }

        return false;
    }
}
