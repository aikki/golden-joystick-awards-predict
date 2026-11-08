<?php

namespace App\Command;

use App\Entity\Award;
use App\Repository\AwardRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:award:set-default',
    description: 'Add a short description for your command',
)]
class AwardSetDefaultCommand{

    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Name')] array $name = [],
        #[Option('Id')] int $id = 0,
        #[Option('Unset All')] bool $unsetAll = false,
    ): int {
        /** @var AwardRepository $repository */
        $repository = $this->entityManager->getRepository(Award::class);

        if ($unsetAll) {
            $repository->unsetAllDefault();

            $io->success('All awards unset');
            return Command::SUCCESS;
        }

        $stringName = implode(' ', $name);

        if (empty($stringName)) {
            if (empty($id)) {
                $io->error('You have to provide Name or Id');
                return Command::FAILURE;
            }
            $award = $repository->find($id);
        } else {
            $award = $repository->findOneBy(['name' => $stringName]);
        }

        if (!$award) {
            $io->error('Award not found');
            return Command::FAILURE;
        }

        $repository->setAsDefault($award);

        $io->success("Award {$award->getName()} set as default");

        return Command::SUCCESS;
    }
}
