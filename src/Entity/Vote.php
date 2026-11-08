<?php

namespace App\Entity;

use App\Repository\VoteRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
class Vote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Voter $voter = null;

    #[ORM\ManyToOne(inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Nominee $nominee = null;

    public function __construct(?Category $category = null, ?Nominee $nominee = null)
    {
        $this->category = $category;
        $this->nominee = $nominee;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVoter(): ?Voter
    {
        return $this->voter;
    }

    public function setVoter(?Voter $voter): static
    {
        $this->voter = $voter;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getNominee(): ?Nominee
    {
        return $this->nominee;
    }

    public function setNominee(?Nominee $nominee): static
    {
        $this->nominee = $nominee;

        return $this;
    }
}
