<?php

namespace App\Command;

use App\Service\RoleService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:make:admin', description: 'create admin user')]
class MakeAdminCommand extends Command
{
    public function __construct(
        private RoleService $roleService
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('user-id', InputArgument::REQUIRED, 'User id');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $userId = (int) $input->getArgument('user-id');
        $this->roleService->grantAdmin($userId);

        return Command::SUCCESS;
    }
}