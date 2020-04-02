<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ApplicationRepository")
 */
class Application implements UserInterface
{
    use EntityTimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Organization", inversedBy="applications")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organization;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $signupId;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Scope")
     */
    private $scopes;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ApiKey", mappedBy="application", orphanRemoval=true)
     */
    private $apiKeys;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserPosition", mappedBy="application", orphanRemoval=true)
     */
    private $userPositions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Subscription", mappedBy="application", orphanRemoval=true)
     */
    private $subscriptions;

    public function __construct()
    {
        $this->scopes = new ArrayCollection();
        $this->apiKeys = new ArrayCollection();
        $this->userPositions = new ArrayCollection();
        $this->subscriptions = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getOrganization(): ?Organization
    {
        return $this->organization;
    }

    public function setOrganization(?Organization $organization): self
    {
        $this->organization = $organization;

        return $this;
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

    public function getSignupId(): ?int
    {
        return $this->signupId;
    }

    public function setSignupId(int $signupId): self
    {
        $this->signupId = $signupId;

        return $this;
    }

    /**
     * @return Collection|Scope[]
     */
    public function getScopes(): Collection
    {
        return $this->scopes;
    }

    public function addScope(Scope $scope): self
    {
        if (!$this->scopes->contains($scope)) {
            $this->scopes[] = $scope;
        }

        return $this;
    }

    public function removeScope(Scope $scope): self
    {
        if ($this->scopes->contains($scope)) {
            $this->scopes->removeElement($scope);
        }

        return $this;
    }

    /**
     * @return Collection|ApiKey[]
     */
    public function getApiKeys(): Collection
    {
        return $this->apiKeys;
    }

    public function addApiKey(ApiKey $apiKey): self
    {
        if (!$this->apiKeys->contains($apiKey)) {
            $this->apiKeys[] = $apiKey;
            $apiKey->setApplication($this);
        }

        return $this;
    }

    public function removeApiKey(ApiKey $apiKey): self
    {
        if ($this->apiKeys->contains($apiKey)) {
            $this->apiKeys->removeElement($apiKey);
            // set the owning side to null (unless already changed)
            if ($apiKey->getApplication() === $this) {
                $apiKey->setApplication(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserPosition[]
     */
    public function getUserPositions(): Collection
    {
        return $this->userPositions;
    }

    public function addUserPosition(UserPosition $userPosition): self
    {
        if (!$this->userPositions->contains($userPosition)) {
            $this->userPositions[] = $userPosition;
            $userPosition->setApplication($this);
        }

        return $this;
    }

    public function removeUserPosition(UserPosition $userPosition): self
    {
        if ($this->userPositions->contains($userPosition)) {
            $this->userPositions->removeElement($userPosition);
            // set the owning side to null (unless already changed)
            if ($userPosition->getApplication() === $this) {
                $userPosition->setApplication(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Subscription[]
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): self
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions[] = $subscription;
            $subscription->setApplication($this);
        }

        return $this;
    }

    public function removeSubscription(Subscription $subscription): self
    {
        if ($this->subscriptions->contains($subscription)) {
            $this->subscriptions->removeElement($subscription);
            // set the owning side to null (unless already changed)
            if ($subscription->getApplication() === $this) {
                $subscription->setApplication(null);
            }
        }

        return $this;
    }

    public function getRoles()
    {
        return ['ROLE_APPLICATION'];
    }

    public function getPassword()
    {
    }

    public function getSalt()
    {
    }

    public function getUsername()
    {
        return $this->name;
    }

    public function eraseCredentials()
    {
    }

    public function __toString()
    {
        return $this->name;
    }
}
