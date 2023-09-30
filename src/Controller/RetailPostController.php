<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;
use App\Entity\Media;

class RetailPostController extends AbstractController
{
    #[Route('/retailpost/{id}', name: 'retail_post')]
    public function index(int $id, EntityManagerInterface $entityManager): Response
    {
        $pictureList = null;
        $videoUrlList = null;

        $postRepo = $entityManager->getRepository(Post::class);
        $post = $postRepo->find($id);

        $mediaRepo = $entityManager->getRepository(Media::class);
        $pictureList = $mediaRepo->pictureList($post->getId());
        $videoUrlList = $mediaRepo->videoUrlList($post->getId());

        // video url display processing
        $urlModifiedList = [];
        foreach ($videoUrlList as $url) {
            $urlModifiedList [] = str_replace('youtu.be', 'youtube.com/embed', $url);
        }

        return $this->render('retail_post/index.html.twig', [
            'controller_name' => 'RetailPostController',
            'post' => $post,
            'pictureList' => $pictureList,
            'videoUrlList' => $urlModifiedList,
        ]);
    }
}
