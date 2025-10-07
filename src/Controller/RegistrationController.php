<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET','POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer): Response
    {
        $user = new User();

        if ($request->isMethod('POST')) {
            $user->setNom($request->request->get('nom'));
            $user->setPrenom($request->request->get('prenom'));
            $user->setEmail($request->request->get('email'));
            $user->setMetier($request->request->get('metier'));
            $user->setFormation($request->request->get('formation'));
            $user->setSpecialite($request->request->get('specialite'));
            $user->setPosteOccupe($request->request->get('poste_occupe'));
            $user->setCv($request->request->get('cv'));
            $user->setDateCreation(new \DateTime());
            $user->setEtatValidation(false);
            $user->setRoles(['ROLE_USER']);

            $hashedPassword = $passwordHasher->hashPassword($user, $request->request->get('password'));
            $user->setPassword($hashedPassword);


            $token = Uuid::v4();
            $user->setVerificationToken($token);

            $entityManager->persist($user);
            $entityManager->flush();

            $url = $this->generateUrl('app_verify_account', ['token' => $token], \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL);

            $emailMessage = (new Email())
                ->from('Clashofclan93440@gmail.com')
                ->to($user->getEmail())
                ->subject('Validez votre compte')
                ->html("<p>Bonjour {$user->getPrenom()},</p>
                    <p>Veuillez valider votre compte en cliquant sur ce lien :</p>
                    <a href='http://{$this->getParameter('nom_domaine')}/verify/{$token}'>Valider mon compte</a>");

            $mailer->send($emailMessage);
            $this->addFlash('success', 'Votre compte a été créé. Un email de confirmation a été envoyé.');
            return $this->redirectToRoute('app_login');
        }
        dump();

        return $this->render('registration/register.html.twig');
    }

    #[Route('/verify/{token}', name: 'app_verify_account', methods: ['GET'])]
    public function verifyUser(string $token, EntityManagerInterface $entityManager): Response
    {

        $user = $entityManager->getRepository(User::class)->findOneBy(['verificationToken' => $token]);

        if (!$user) {

            $this->addFlash('danger', 'L\'URL de validation est invalide ou a expiré.');
            return $this->redirectToRoute('app_login');
        }


        $user->setEtatValidation(true);
        $user->setVerificationToken(null); // Le token est utilisé et doit être effacé


        $entityManager->flush();

        $this->addFlash('success', 'Félicitations ! Votre compte a été validé. Vous pouvez maintenant vous connecter.');
        return $this->redirectToRoute('app_login');
    }
}
