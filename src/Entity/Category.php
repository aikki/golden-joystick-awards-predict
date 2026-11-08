<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Nominee>
     */
    #[ORM\OneToMany(targetEntity: Nominee::class, mappedBy: 'category', cascade: ['persist'], orphanRemoval: true)]
    private Collection $nominees;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'categories')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Award $award = null;

    private ?int $_cachedShouldBeDoubleWidth = null;

    /**
     * @var Collection<int, Vote>
     */
    #[ORM\OneToMany(targetEntity: Vote::class, mappedBy: 'category')]
    private Collection $votes;

    public function __construct(?string $name = null)
    {
        $this->nominees = new ArrayCollection();
        $this->name = $name;
        $this->votes = new ArrayCollection();
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

    /**
     * @return Collection<int, Nominee>
     */
    public function getNominees(): Collection
    {
        return $this->nominees;
    }

    public function addNominee(Nominee $nominee): static
    {
        if (!$this->nominees->contains($nominee)) {
            $this->nominees->add($nominee);
            $nominee->setCategory($this);
        }

        return $this;
    }

    public function removeNominee(Nominee $nominee): static
    {
        if ($this->nominees->removeElement($nominee)) {
            // set the owning side to null (unless already changed)
            if ($nominee->getCategory() === $this) {
                $nominee->setCategory(null);
            }
        }

        return $this;
    }

    public function getAward(): ?Award
    {
        return $this->award;
    }

    public function setAward(?Award $award): static
    {
        $this->award = $award;

        return $this;
    }

    public function getLongestNominee(): ?Nominee
    {
        return $this->nominees->reduce(fn(?Nominee $n, Nominee $m) => mb_strlen($n ? $n->getName() : '') > mb_strlen($m->getName()) ? $n : $m, null);;
    }

    public function shouldBeDoubleWidth(): bool
    {
        if ($this->_cachedShouldBeDoubleWidth === null) {
            $this->_cachedShouldBeDoubleWidth = mb_strlen($this->getLongestNominee()->getName()) > 34;
        }
        return $this->_cachedShouldBeDoubleWidth;
    }

    /**
     * @return Collection<int, Vote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): static
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
            $vote->setCategory($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): static
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getCategory() === $this) {
                $vote->setCategory(null);
            }
        }

        return $this;
    }

    public function getRandomNominee(): ?Nominee
    {
        $nominees = $this->nominees->toArray();

        return $nominees
            ? $nominees[array_rand($nominees)]
            : null;
    }

    public function getWinner(): ?Nominee
    {
        return $this->nominees->filter(fn(Nominee $nominee) => $nominee->isWinner())->first() ?: null;
    }

    public function setWinner(?Nominee $winner): static
    {
        ($this->nominees->filter(fn(Nominee $nominee) => $nominee->isWinner())->first() ?: null)?->setWinner(false);
        $winner?->setWinner(true);
        return $this;
    }
}
