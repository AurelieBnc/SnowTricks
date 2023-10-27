<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;
use App\Entity\Media;
use App\Entity\Comment;


class PostController extends AbstractController
{
    #[Route('/post/{id}', name: 'post')]
    public function index(int $id, EntityManagerInterface $entityManager): Response
    {
        //$hasAccess = $this->isGranted('ROLE_ADMIN');

        $pictureList = null;
        $videoUrlList = null;
        $commentList = null;

        $postRepo = $entityManager->getRepository(Post::class);
        $post = $postRepo->find($id);

        $mediaRepo = $entityManager->getRepository(Media::class);
        $pictureList = $mediaRepo->pictureList($post->getId());
        $videoUrlList = $mediaRepo->videoUrlList($post->getId());

        $commentRepo = $entityManager->getRepository(Comment::class);
        $commentList = $commentRepo->commentList($post->getId());

        // video url display processing
        $urlModifiedList = [];
        foreach ($videoUrlList as $url) {
            $urlModifiedList [] = str_replace('youtu.be', 'youtube.com/embed', $url);
        }

        return $this->render('post/index.html.twig', [
            'post' => $post,
            'pictureList' => $pictureList,
            'videoUrlList' => $urlModifiedList,
            'commentList' => $commentList
        ]);
    }


}
