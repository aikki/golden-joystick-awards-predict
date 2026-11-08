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
use Symfony\Component\Routing\RouterInterface;

#[AsCommand(
    name: 'app:award:rehash',
    description: 'Add a short description for your command',
)]
class AwardRehashCommand{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private RouterInterface $router,
    ) {}

    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Name')] array $name = [],
        #[Option('Id')] int $id = 0,
    ): int {
        /** @var AwardRepository $repository */
        $repository = $this->entityManager->getRepository(Award::class);

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

        $award->setAdminKey(bin2hex(random_bytes(32)));

        $this->entityManager->persist($award);
        $this->entityManager->flush();

        $io->outlineSuccess("New dashboard access URL for {$award->getName()}: " .  $this->router->generate('app_access', ['admin_token' => $award->getAdminKey()], RouterInterface::ABSOLUTE_URL));

        return Command::SUCCESS;
    }
}
