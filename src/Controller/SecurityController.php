<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Service\JWTService;
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
    public function registration(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SendEmailService $email, JWTService $jwt): Response
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
            $user->setIsVerified(false);

            $entityManager->persist($user);
            $entityManager->flush();

            // Generate JWT Token
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256',
            ];
            $payload = ['user_id' => $user->getId()];
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            // Send Verification Email
            $email->send(
                'aurelie.test.mail@gmail.com',
                $user->getEmail(),
                'Confirmation de ton email',
                'security/confirm_email.html.twig',
                [ 'user' => $user, 'token' => $token ]
            );
            return $this->redirectToRoute('app_home');
        }
        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/verification/{token}', name: 'app_verify_email')]
    public function verifyUserEmail(string $token, JWTService $jwt, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret')) ) {
            $payload = $jwt->getPayload($token);
            $user = $userRepository->find($payload['userId']);

            if ($user && $user->isVerified() === false) {
                $user->setIsVerified(true);
                $entityManager->flush($user);

                return $this->redirectToRoute('app_login');
            }
        }
        $this->addFlash('error', 'une erreur est survenue lors de la vérification du Token, merci de nous contacter!');
        
        return $this->redirectToRoute('app_home');
    }

    #[Route('/renvoiVerification', name: 'app_retry_verif_email')]
    public function sendBackVerifyUserEmail(SendEmailService $email, JWTService $jwt, UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('verification', 'Tu dois être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        if($user->isVerified()){
            $this->addFlash('verification', 'Ton email est déjà vérifié !');
            return $this->redirectToRoute('app_login');
        }

        // Generate JWT Token
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS256',
        ];
        $payload = ['user_id' => $user->getId()];
        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        // Send Verification Email
        $email->send(
            'aurelie.test.mail@gmail.com',
            $user->getEmail(),
            'Confirmation de ton email',
            'security/confirm_email.html.twig',
            [ 'user' => $user, 'token' => $token ]
        );

        $this->addFlash('success', 'Le nouveau lien a bien été envoyé !');
        return $this->redirectToRoute('app_home');
    }

}
