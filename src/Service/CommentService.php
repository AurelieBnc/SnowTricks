<?php

namespace App\Service;

use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Comment;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;


class CommentService 
{
    private $entityManager;
    private $formFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
    ) {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function getCommentForm(Request $request, ?User $user, Trick $trick): FormInterface|RedirectResponse 
    {
        $comment = new Comment;
        $commentForm = $this->formFactory->create(CommentType::class, $comment);

        if ($user) {
            $commentForm->handleRequest($request);

            if ($commentForm->isSubmitted() && $commentForm->isValid()) {
                if ($user->isVerified() === false) {
                    $request->getSession()->getFlashBag()->add('verification', 'Tu dois confirmer ton adresse email.');
                } else {
                    $this->processCommentSubmission($comment, $trick, $user, $request);
                }
            }
        } else {
            $request->getSession()->getFlashBag()->add('login', 'Tu dois être connecté pour envoyer un commentaire.');
        }

        return $commentForm;
    }

    private function processCommentSubmission(Comment $comment, Trick $trick, User $user, Request $request): void
    {
        $comment
            ->setCreatedAt(new \DateTimeImmutable())
            ->setTrick($trick)
            ->setUser($user);

        $this->entityManager->persist($comment);
        $this->entityManager->flush();

        $request->getSession()->getFlashBag()->add('success', 'Ton commentaire a bien été envoyé !');
    }
    
}
