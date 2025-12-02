<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\AST\Functions\CurrentDateFunction;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'hsp:create:admin',
    description: 'Crée un nouvel utilisateur administrateur',
)]
class CreateUserAdminCommand extends Command
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private EntityManagerInterface $entityManager;

    public function __construct(
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $io = new SymfonyStyle($input, $output);

        $io->title("Création d'un utilisateur ADMINISTRATEUR");

        $nom = $helper->ask($input, $output, new Question('Nom : '));
        $prenom = $helper->ask($input, $output, new Question('Prénom : '));


        do {
            $email = $helper->ask($input, $output, new Question('Email : '));
            $existingUser = $this->entityManager
                ->getRepository(User::class)
                ->findOneBy(['email' => $email]);

            if ($existingUser) {
                $io->error("❌ Cet email est déjà utilisé.");
                $email = null;
            }
        } while (!$email);

        $passwordQuestion = new Question('Mot de passe : ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $passwordQuestion);


        $user = new User();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setEmail($email);
        $user->setVerificationToken(null);
        $user->setMetier("GESTIONNAIRE");
        $user->setEtatValidation(1);
        $user->setDateCreation(new \DateTime());


        $user->setPassword(
            $this->userPasswordHasher->hashPassword($user, $password)
        );
        $user->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success("✅ Utilisateur administrateur créé avec succès.");

        return Command::SUCCESS;
    }
}
