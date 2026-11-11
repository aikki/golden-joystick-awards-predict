<?php

namespace App\Entity;

use App\Repository\AwardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AwardRepository::class)]
class Award
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $close_date = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'award', cascade: ['persist'], orphanRemoval: true)]
    private Collection $categories;

    /**
     * @var Collection<int, Voter>
     */
    #[ORM\OneToMany(targetEntity: Voter::class, mappedBy: 'award', orphanRemoval: true)]
    private Collection $voters;

    #[ORM\Column]
    private ?bool $isDefault = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $admin_key = null;

    public function __construct($name = null)
    {
        $this->categories = new ArrayCollection();
        $this->voters = new ArrayCollection();
        $this->name = $name;
        $this->admin_key = bin2hex(random_bytes(32));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCloseDate(): ?\DateTime
    {
        return $this->close_date;
    }

    public function setCloseDate(?\DateTime $close_date): static
    {
        $this->close_date = $close_date;

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setAward($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getAward() === $this) {
                $category->setAward(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Voter>
     */
    public function getVoters(): Collection
    {
        return $this->voters;
    }

    public function addVoter(Voter $voter): static
    {
        if (!$this->voters->contains($voter)) {
            $this->voters->add($voter);
            $voter->setAward($this);
        }

        return $this;
    }

    public function removeVoter(Voter $voter): static
    {
        if ($this->voters->removeElement($voter)) {
            // set the owning side to null (unless already changed)
            if ($voter->getAward() === $this) {
                $voter->setAward(null);
            }
        }

        return $this;
    }

    public function isDefault(): ?bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): static
    {
        $this->isDefault = $isDefault;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getRanking(): array {
        $voters = $this->getVoters()->toArray();
        usort(
            $voters,
            fn (Voter $a, Voter $b): int => $b->getWinnerVotes()->count() <=> $a->getWinnerVotes()->count()
        );
        return $voters;
    }

    public function getAdminKey(): ?string
    {
        return $this->admin_key;
    }

    public function setAdminKey(?string $admin_key): static
    {
        $this->admin_key = $admin_key;

        return $this;
    }

    public function isClosed(): bool
    {
        return $this->getCloseDate() && $this->getCloseDate() < new \DateTime();
    }
}
