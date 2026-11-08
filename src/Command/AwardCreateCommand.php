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
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\RouterInterface;

#[AsCommand(
    name: 'app:award:create',
    description: 'Add a short description for your command',
)]
readonly class AwardCreateCommand {
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private string $projectDir,
        private EntityManagerInterface $entityManager,
        #[Autowire('%env(DEFAULT_URI)%')]
        private string $defaultUri,
        private RouterInterface $router,
    ) {}

    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Json Path')] string $jsonPath,
        #[Argument('Name')] array $name,
    ): int {
        $stringName = implode(' ', $name);

        if (empty($stringName)) {
            $io->error('Name cannot be empty');
            return Command::FAILURE;
        }

        if (!file_exists($jsonPath)) {
            $jsonPath = $this->projectDir . '/resources/awards/' . $jsonPath;

            if (!file_exists($jsonPath)) {
                $io->error('File does not exist');
                return Command::FAILURE;
            }
        }

        $check = $this->entityManager->getRepository(Award::class)->findOneBy(['name' => $stringName]);
        if ($check) {
            if (!$io->confirm('Award with this name already exists, do you really want to continue?', false)) {
                return Command::FAILURE;
            }
        }

        $json = file_get_contents($jsonPath);

        if (!json_validate($json)) {
            $io->error('Invalid JSON');
            return Command::FAILURE;
        }

        $data = json_decode($json, true);

        $description = $io->ask('Enter description:');

        $award = new Award($stringName);

        $award->setDescription($description);

        $categories = array_map(function($value) use ($award) {
            $category = new Category($value['name']);
            $award->addCategory($category);
            foreach ($value['nominees'] as $nominee) {
                $nominee = new Nominee($nominee);
                $category->addNominee($nominee);
            }
            return $category;

        }, $data);

        $this->entityManager->persist($award);

        $this->entityManager->flush();

        $io->success('Award created successfully');
        $io->outlineSuccess('Dashboard URL: ' .  $this->router->generate('app_admin', ['admin_token' => $award->getAdminKey()], RouterInterface::ABSOLUTE_URL));

        return Command::SUCCESS;
    }
}
