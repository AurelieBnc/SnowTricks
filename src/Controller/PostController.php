<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;
use App\Entity\Media;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Form\CommentType;
use App\Form\PostFormType;
use App\Service\PictureService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class PostController extends AbstractController
{
    #[Route('/post/create', name: 'post_create')]
    #[Route('/post/{id}/edit', name: 'post_edit')]
    public function post(Post $post = null, Request $request, EntityManagerInterface $entityManager, PictureService $pictureService){
        if (!$post) {
                $post = new Post;
        }

        $postForm = $this->createForm(PostFormType::class, $post);
        $postForm->handleRequest($request);

        if ($postForm->isSubmitted() && $postForm->isValid()) {
            if ($this->getUser()) {
                // if ($this->getUser()->is_granted('ROLE_ADMIN') === false) {
                //     $this->addFlash('verification', 'Vous devez être administrateur pour créer un article.');
                //     $this->redirectToRoute('login');
                // }
//todo ajouter erreur non connecté + authorisation
                if (!$post->getId()) {
                    $post->setCreatedAt(new \DateTimeImmutable()); 
                } else {
                    $post->setUpdateDate(new \DateTimeImmutable()); 
                }
                
                $pictureList = $postForm->get('pictureList')->getData();
                
                foreach ($pictureList as $picture) {
                    $folder = 'postImages';
                    $field = $pictureService->add($picture, $folder);
                    dump($field); 

                    $picture = new Picture;
                    $picture->setName($field);
                    $post->addPicture($picture);
                }

                $media = new Media;
                $url = $postForm->get('media')->getData();
                $media->setVideoUrl($url);
                $post->addMedia($media);

                $entityManager->persist($post);
                $entityManager->flush();

                $this->addFlash('success', 'Ton article a bien été ajouté !');

                return $this->redirectToRoute('app_home');
            } else {
                $this->addFlash('login', 'Vous devez être connecté pour envoyer un commentaire.');
            }
        }

        return $this->render('post/post.html.twig', [
            'form' => $postForm,
            'editMode' => $post->getId() !== null,
        ]);
    }

    #[Route('/post/{id}/{loader}', name: 'post')]
    public function index(int $id, int $loader, EntityManagerInterface $entityManager, Request $request, ?UserInterface $user): Response
    {
        $pictureList = null;
        $videoUrlList = null;
        $commentList = null;

        $postRepo = $entityManager->getRepository(Post::class);
        $post = $postRepo->find($id);

        $mediaRepo = $entityManager->getRepository(Media::class);
        $videoUrlList = $mediaRepo->videoUrlList($post->getId());

        $pictureRepo = $entityManager->getRepository(Picture::class);
        $pictureList = $pictureRepo->pictureList($post->getId()); 

        $commentRepo = $entityManager->getRepository(Comment::class);
        $countCommentList = $commentRepo->countCommentList($post->getId());

        if ($loader === 1) {
            $loader = ($countCommentList > 10) ? 1 : 0;
        }

        if ($loader === 0) {
            $commentList = $commentRepo->commentList($post->getId());
        } else {   
            $commentList = $commentRepo->commentListMaxTen($post->getId());
        }

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
            if ($user) {
                if ($user->isVerified() === false) {
                    $this->addFlash('verification', 'Vous devez confirmer votre adresse email.');
                    $this->redirectToRoute('post', [
                        'id' => $post->getId(),
                        'loader' => $loader
                    ]);
                }
                $comment->setCreatedAt(new \DateTimeImmutable())
                    ->setPost($post)
                    ->setUser($user);
                $entityManager->persist($comment);
                $entityManager->flush();

                return $this->redirectToRoute('post', [
                    'id' => $post->getId(),
                    'loader' => $loader
            ]);
            } else {
                $this->addFlash('login', 'Vous devez être connecté pour envoyer un commentaire.');
            }
        }

        return $this->render('post/index.html.twig', [
            'post' => $post,
            'pictureList' => $pictureList,
            'videoUrlList' => $urlModifiedList,
            'commentList' => $commentList,
            'loader' => $loader,
            'commentForm' => $commentForm->createView()
        ]);
    }

    #[Route('/commentList/{post}/{loader}', name: 'complete_comment_list')]
    public function commentListRedirect(int $post, int $loader): Response
    {
        return $this->redirectToRoute('post', [
            'id' => $post,
            'loader' => 0
        ]);
    }



}
