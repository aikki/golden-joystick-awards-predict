<?php

namespace App\Command;

use App\Entity\Award;
use App\Entity\Category;
use App\Entity\Nominee;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:award:set-random-winners',
    description: 'Add a short description for your command',
)]
class AwardSetRandomWinnersCommand{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Id')] int $id,
    ): int {
        $award = $this->entityManager->getRepository(Award::class)->find($id);

        $subQuery = $this->entityManager->createQueryBuilder()
            ->select('c.id')
            ->from(Category::class, 'c')
            ->where('IDENTITY(c.award) = :awardId');

        $this->entityManager->getRepository(Nominee::class)
            ->createQueryBuilder('n')
            ->update()
            ->set('n.winner', ':value')
            ->where('IDENTITY(n.category) IN (' . $subQuery->getDQL() . ')')
            ->setParameter('value', false)
            ->setParameter('awardId', $id)
            ->getQuery()
            ->execute();

        foreach ($award->getCategories() as $category) {
            $nominee = $category->getRandomNominee();
            $nominee->setWinner(true);
            $this->entityManager->persist($nominee);
        }
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
