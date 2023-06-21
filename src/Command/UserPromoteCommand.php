<?php

namespace App\Command;

use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:user:promote',
    description: 'Commande pour promote un user',
)]
class UserPromoteCommand extends Command
{
    private UserRepository $UserRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->UserRepository = $userRepository;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('UserID', InputArgument::OPTIONAL, 'ID de l\'utilisateur')
            ->addArgument('UserRole', InputArgument::OPTIONAL, 'Role à donner à l\'utilisateur')
//            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $UID = $input->getArgument('UserID');
        $URole = $input->getArgument('UserRole');

        $User = $this->UserRepository->findOneById($UID);
        
        if ($User == null) {
            $io->error("L'id de l'utilisateur n'est pas connu");
            return Command::FAILURE;
        }

        if ($URole == null) {
            $io->error("Pas de rôle sélectionné");
            return Command::FAILURE;
        }

        if ($URole) {
            $User->setRoles([$URole]);
            $this->UserRepository->save($User, true);
        }

        $io->success('L\'utilisateur a bien été promu au rôle '.$URole);

        return Command::SUCCESS;
    }
}