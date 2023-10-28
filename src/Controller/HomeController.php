<?php

namespace App\Controller;

use App\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;
use Symfony\Component\Security\Core\User\UserInterface;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager, ?UserInterface $user): Response
    {
        if ($user) {
            if ($user->isVerified() === false) {
                $this->addFlash('verification', 'Vous devez confirmer votre adresse email.');
                $this->redirectToRoute('app_home');
            }
            if (!$user->isVerified() === false) {
                $this->addFlash('succes', 'Une bonne journée à toi '.$user->getUsername().' coeur coeur!');
                $this->redirectToRoute('app_home');
            }
        }

        $repo = $entityManager->getRepository(Post::class);
        $postList = $repo->postListMaxFifteen();
        $countPostList = $repo->countPostList();
        $loader = true;

        $mediaRepo = $entityManager->getRepository(Media::class);
        $pictureList = $mediaRepo->findAll();

        return $this->render('home/index.html.twig', [
            'postList' => $postList,
            'countPostList' => $countPostList,
            'loader' => $loader,
            'pictureList' => $pictureList,

        ]);
    }

    #[Route('/postList', name: 'complete_post_list')]
    public function postList(EntityManagerInterface $entityManager): Response
    {
        $repo = $entityManager->getRepository(Post::class);
        $postList = $repo->findAll();

        $mediaRepo = $entityManager->getRepository(Media::class);
        $pictureList = $mediaRepo->findAll();

        return $this->render('home/index.html.twig', [
            'postList' => $postList,
            'pictureList' => $pictureList,
        ]);
    }
}
