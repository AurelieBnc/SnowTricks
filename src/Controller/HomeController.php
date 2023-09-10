<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $repo = $entityManager->getRepository(Post::class);
        $postList = $repo->postListMaxFifteen();
        $countPostList = $repo->countPostList();
        $loader = true;

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'postList' => $postList,
            'countPostList' => $countPostList,
            'loader' => $loader
        ]);
    }

    #[Route('/postList', name: 'complete_post_list')]
    public function postList(EntityManagerInterface $entityManager): Response
    {
        $repo = $entityManager->getRepository(Post::class);
        $postList = $repo->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'postList' => $postList,
        ]);
    }
}
