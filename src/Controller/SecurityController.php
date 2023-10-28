<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Service\SendEmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


class SecurityController extends AbstractController
{


    #[Route('/inscription', name: 'security_registration')]
    public function registration(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SendEmailService $email): Response
    {
        $user = new User;
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            // Send Email
            $email->send(
                'aurelie.test.mail@gmail.com',
                $user->getEmail(),
                'Confirmation de ton email',
                'security/confirm_email.html.twig',
                [ 'user' => $user ]
            );

            return $this->redirectToRoute('app_home');
        }



        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/verification/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request): Response
    {

    }
}
