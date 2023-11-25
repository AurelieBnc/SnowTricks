<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\RegistrationType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use App\Service\JWTService;
use App\Service\PictureService;
use App\Service\SendEmailService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

#[Route('/security')]
class SecurityController extends AbstractController
{
    #[Route('/registration', name: 'security_registration')]
    public function registration(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SendEmailService $email, JWTService $jwt, PictureService $pictureService): Response
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

            if (null !== $form->get('avatar')->getData()) {
                // upload avatar
                $avatar = $form->get('avatar')->getData();
                $folder = 'avatars';

                $field = $pictureService->add($avatar, $folder, 300, 300);

                $user->setAvatar($field); 
            }

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
                'security/email/confirm_email.html.twig',
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
            $user = $userRepository->find($payload['user_id']);

            if ($user && $user->isVerified() === false) {
                $user->setIsVerified(true);
                $entityManager->flush($user);

                return $this->redirectToRoute('app_login');
            }
        }

        $this->addFlash('error', 'une erreur est survenue lors de la vérification du Token, merci de nous contacter!');
        
        return $this->redirectToRoute('app_home');
    }

    #[Route('/verification-returns', name: 'app_retry_verif_email')]
    public function sendBackVerifyUserEmail(SendEmailService $email, JWTService $jwt, UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('verification', 'Tu dois être connecté pour accéder à cette page');

            return $this->redirectToRoute('app_login');
        }

        if ($user->isVerified()) {
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
            'security/email/confirm_email.html.twig',
            [ 'user' => $user, 'token' => $token ]
        );

        $this->addFlash('success', 'Le nouveau lien a bien été envoyé !');
        
        return $this->redirectToRoute('app_home');
    }

    #[Route('/reset-password-request', name: 'app_forgot_password_request')]
    public function forgottenPasswordRequest(Request $request, UserRepository $userRepository, TokenGeneratorInterface $tokenGeneratorInterface, EntityManagerInterface $entityManager, SendEmailService $email ): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $userRepository->findOneByEmail($form->get('email')->getData());

            if ($user) {
                $resetToken = $tokenGeneratorInterface->generateToken();
                $user->setResetToken($resetToken);

                $entityManager->persist($user);
                $entityManager->flush();
                
                $url = $this->generateUrl('app_reset_password', ['token' => $resetToken], UrlGeneratorInterface::ABSOLUTE_URL);

                $context = [
                    'url' => $url,
                    'user' => $user,
                ];

                $email->send(
                    'aurelie.test.mail@gmail.com',
                    $user->getEmail(),
                    'Ta demande de changement de mot de passe !',
                    'security/email/reset_password_email.html.twig',
                    $context
                );

                return $this->render('security/check_email.html.twig', [
                    'resetToken' => $resetToken,
                ]);

            } else {
                //todo: gérer le fait qu'il n'y a pas d'utilisateur de façon sécuriser (redirect sur le template de réussite de l'envoi du mail)
                // générer un faux token
            }

        }

        return $this->render('security/reset_password_request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function reset(string $token, Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = $userRepository->findOneByResetToken($token);

        if ($user) {
            $form = $this->createForm(ChangePasswordFormType::class);
            $form->handleRequest($request);

            if ($form->issubmitted() && $form->isValid()) {
                $user->setResetToken('');

                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('app_login');

            }

            return $this->render('security/reset_password.html.twig', [
                'resetForm' => $form,
            ]);
        }
        $this->addFlash('error', 'Lien de vérification invalide');

        return $this->redirectToRoute('app_retry_verif_email');
    }

}
