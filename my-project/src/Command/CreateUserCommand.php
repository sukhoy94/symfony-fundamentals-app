<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Create a new user!',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private UserPasswordHasherInterface $userPasswordHasher,
        private UserRepository $userRepository
    )
    {
        parent::__construct();
    }
    
    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $user = new User();
        
        $email = is_string($input->getArgument('email')) ? $input->getArgument('email') : '';
        $password = is_string($input->getArgument('password')) ? $input->getArgument('password') : '';
        
        $user->setEmail($email);
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $password
            )
        );        
        $this->userRepository->save($user, true);
        $io->success(sprintf('User %s account was created!', $email));
        
        return Command::SUCCESS;
    }
}
