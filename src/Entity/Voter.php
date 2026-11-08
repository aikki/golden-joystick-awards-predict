<?php

namespace App\Entity;

use App\Repository\VoterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoterRepository::class)]
class Voter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'voters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Award $award = null;

    #[ORM\Column]
    private ?\DateTime $date_voted = null;

    /**
     * @var Collection<int, Vote>
     */
    #[ORM\OneToMany(targetEntity: Vote::class, mappedBy: 'voter', cascade: ['persist'], orphanRemoval: true)]
    private Collection $votes;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $edit_token = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $signature_font = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $show_token = null;

    public function __construct()
    {
        $this->votes = new ArrayCollection();
    }

    public static function create(Award $award): self
    {
        $voter = new self();
        $voter->setAward($award);
        $voter->setDateVoted(new \DateTime());
        $voter->setEditToken(bin2hex(random_bytes(16)));
        $voter->setShowToken(bin2hex(random_bytes(16)));
        return $voter;
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

    public function getAward(): ?Award
    {
        return $this->award;
    }

    public function setAward(?Award $award): static
    {
        $this->award = $award;

        return $this;
    }

    public function getDateVoted(): ?\DateTime
    {
        return $this->date_voted;
    }

    public function setDateVoted(\DateTime $date_voted): static
    {
        $this->date_voted = $date_voted;

        return $this;
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
            $vote->setVoter($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): static
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getVoter() === $this) {
                $vote->setVoter(null);
            }
        }

        return $this;
    }

    public function addVotePlain(Category $category, Nominee $nominee): static
    {
        $vote = new Vote($category, $nominee);
        $this->addVote($vote);
        return $this;
    }

    public function getEditToken(): ?string
    {
        return $this->edit_token;
    }

    public function setEditToken(?string $edit_token): static
    {
        $this->edit_token = $edit_token;

        return $this;
    }

    public function getSignatureFont(): ?string
    {
        return $this->signature_font;
    }

    public function setSignatureFont(?string $signature_font): static
    {
        $this->signature_font = $signature_font;

        return $this;
    }

    public function getShowToken(): ?string
    {
        return $this->show_token;
    }

    public function setShowToken(?string $show_token): static
    {
        $this->show_token = $show_token;

        return $this;
    }

    public function getWinnerVotes(): Collection
    {
        return $this->votes->filter(fn(Vote $vote) => $vote->getNominee()->isWinner());
    }
}
