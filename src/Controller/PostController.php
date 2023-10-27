<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;
use App\Entity\Media;
use App\Entity\Comment;
use App\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;

class PostController extends AbstractController
{
    #[Route('/post/{id}', name: 'post')]
    public function index(int $id, EntityManagerInterface $entityManager, Request $request): Response
    {
        // $hasAccess = $this->isGranted('ROLE_ADMIN');

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

        // Video url display processing
        $urlModifiedList = [];
        foreach ($videoUrlList as $url) {
            $urlModifiedList [] = str_replace('youtu.be', 'youtube.com/embed', $url);
        }

        // Create a commentForm
        $comment = new Comment;
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setCreatedAt(new \DateTimeImmutable())
                    ->setPost($post)
                    ->setAuthor('moi');
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('post', [
                'id' => $post->getId()
            ]);
        }

        return $this->render('post/index.html.twig', [
            'post' => $post,
            'pictureList' => $pictureList,
            'videoUrlList' => $urlModifiedList,
            'commentList' => $commentList,
            'commentForm' => $commentForm->createView()
        ]);
    }

}
