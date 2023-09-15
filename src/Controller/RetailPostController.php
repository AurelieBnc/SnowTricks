<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;
use App\Entity\Media;
use Exception;

class RetailPostController extends AbstractController
{
    #[Route('/retailpost/{id}', name: 'retail_post')]
    public function index(int $id, EntityManagerInterface $entityManager): Response
    {
        $postRepo = $entityManager->getRepository(Post::class);
        $post = $postRepo->find($id);

        $mediaRepo = $entityManager->getRepository(Media::class);
        $mediaList = $mediaRepo->findByPost($post->getId());

        return $this->render('retail_post/index.html.twig', [
            'controller_name' => 'RetailPostController',
            'post' => $post,
            'mediaList' => $mediaList,
        ]);
    }
}
