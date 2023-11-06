<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Post;
use App\Entity\Media;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Form\CommentType;
use App\Form\CreatePostFormType;
use App\Form\MediaType;
use App\Form\PictureType;
use App\Service\PictureService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class PostController extends AbstractController
{
    #[Route('/post/create', name: 'post_create')]
    public function createPost(Request $request, EntityManagerInterface $entityManager, PictureService $pictureService): Response
    {
        $user = $this->getUser();
        $post = new Post;

        $postForm = $this->createForm(CreatePostFormType::class, $post);
        $postForm->handleRequest($request);

        if ($postForm->isSubmitted() && $postForm->isValid()) {
            if ($user) {
                // if ($this->getUser()->is_granted('ROLE_ADMIN') === false) {
                //     $this->addFlash('verification', 'Vous devez être administrateur pour créer un article.');
                //     $this->redirectToRoute('login');
                // }
                if ($user->isVerified() === false) {
                    $this->addFlash('verification', 'Vous devez confirmer votre adresse email.');
                    $this->redirectToRoute('app_home', [
                    ]);
                }
            //todo ajouter erreur non connecté + authorisation

                $post->setCreatedAt(new \DateTimeImmutable()); 
                
                $pictureList = $postForm->get('pictureList')->getData();
                
                foreach ($pictureList as $picture) {
                    $folder = 'postImages';
                    $field = $pictureService->add($picture, $folder);

                    $picture = new Picture;
                    $picture->setName($field);
                    $post->addPicture($picture);
                }

                if (null !== $postForm->get('media')->getData()) {
                    $media = new Media;
                    $url = $postForm->get('media')->getData();
                    $media->setVideoUrl($url);
                    $post->addMedia($media);
                }

                $entityManager->persist($post);
                $entityManager->flush();

                $this->addFlash('success', 'Ton article a bien été ajouté !');

                return $this->redirectToRoute('app_home');
            } else {
                $this->addFlash('login', 'Vous devez être connecté pour écrire un article.');
            }
        }

        return $this->render('post/create_post.html.twig', [
            'form' => $postForm,
        ]);
    }

    #[Route('/post/{post_id}/edit/picture/{id}', name: 'post_edit_picture')]
    public function editPicture(int $post_id, int $id, Request $request, EntityManagerInterface $entityManager, PictureService $pictureService ): Response
    {
        $user = $this->getUser();

        $postRepo = $entityManager->getRepository(Post::class);
        $post = $postRepo->find($post_id);

        $pictureRepo = $entityManager->getRepository(Picture::class);
        $picture = $pictureRepo->find($id);

        $editPicture = new Picture;

        $pictureForm = $this->createForm(PictureType::class, $editPicture);
        $pictureForm->handleRequest($request);
        
        if ($pictureForm->isSubmitted() && $pictureForm->isValid()) {
            if ($user) {
                if ($user->isVerified() === false) {
                    $this->addFlash('verification', 'Vous devez confirmer votre adresse email.');
                    $this->redirectToRoute('app_home', [
                    ]);
                }

                $picturedata = $pictureForm->get('name')->getData();
                $folder = 'postImages';
                $field = $pictureService->add($picturedata, $folder);
                $pictureService->delete($picture->getName(), $folder);

                $picture->setName($field);
                $picture->setPost($post);
                $post->addPicture($picture);

                $entityManager->persist($picture);
                $entityManager->flush();

                return $this->redirectToRoute('post_edit', [
                    'id' => $post->getId(),
                ]);
            } else {
                $this->addFlash('login', 'Vous devez être connecté pour envoyer un commentaire.');
            }
        }

        return $this->render('post/edit/edit_picture.html.twig', [
            'form' => $pictureForm,
            'picture' => $picture,
        ]);

    }

    #[Route('/post/{post_id}/edit/media/{id}', name: 'post_edit_media')]
    public function editMedia(int $post_id, int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        //todo flusher l'url media au début pour éviter de la modifier a chaque display
        $user = $this->getUser();

        $postRepo = $entityManager->getRepository(Post::class);
        $post = $postRepo->find($post_id);

        $mediaRepo = $entityManager->getRepository(Media::class);
        $media = $mediaRepo->find($id);

        $editMedia = new Media;

        $mediaForm = $this->createForm(MediaType::class, $editMedia);
        $mediaForm->handleRequest($request);

        if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {
            if ($user) {
                if ($user->isVerified() === false) {
                    $this->addFlash('verification', 'Vous devez confirmer votre adresse email.');
                    $this->redirectToRoute('app_home', [
                    ]);
                }

                $modifyUrl = str_replace('youtu.be', 'youtube.com/embed', $editMedia->getVideoUrl());
                $media->setVideoUrl($modifyUrl); 

                // $mediaData = $mediaForm->get('videoUrl')->getData();

                // $media->setvideoUrl($mediaData);
                $media->setPost($post);
                $post->addMedia($media);

                $entityManager->persist($media);
                $entityManager->flush();

                return $this->redirectToRoute('post_edit', [
                    'id' => $post->getId(),
                ]);
            } else {
                $this->addFlash('login', 'Vous devez être connecté pour modifier un lien.');
            }
        }

        $modifyUrl = str_replace('youtu.be', 'youtube.com/embed', $media->getVideoUrl());
        $media->setVideoUrl($modifyUrl);

        return $this->render('post/edit/edit_media.html.twig', [
            'form' => $mediaForm,
            'media' => $media,
        ]);

    }

    #[Route('/post/{id}/edit', name: 'post_edit')]
    public function editPost(int $id, Request $request, EntityManagerInterface $entityManager, PictureService $pictureService): Response
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

        // Video url display processing
        $urlModifiedList = [];
        foreach ($videoUrlList as $url) {
            
            $modifyUrl = str_replace('youtu.be', 'youtube.com/embed', $url->getVideoUrl());
            $url->setVideoUrl($modifyUrl);
        }

        $postForm = $this->createForm(CreatePostFormType::class, $post);
        $postForm->handleRequest($request);

        if ($postForm->isSubmitted() && $postForm->isValid()) {
            if ($this->getUser()) {
                // if ($this->getUser()->is_granted('ROLE_ADMIN') === false) {
                //     $this->addFlash('verification', 'Vous devez être administrateur pour créer un article.');
                //     $this->redirectToRoute('login');
                // }

                $post->setCreatedAt(new \DateTimeImmutable()); 
                
                $pictureList = $postForm->get('pictureList')->getData();
                
                foreach ($pictureList as $picture) {
                    $folder = 'postImages';
                    $field = $pictureService->add($picture, $folder);

                    $picture = new Picture;
                    $picture->setName($field);
                    $post->addPicture($picture);
                }

                if (null !== $postForm->get('media')->getData()) {
                    $media = new Media;
                    $url = $postForm->get('media')->getData();
                    $media->setVideoUrl($url);
                    $post->addMedia($media);
                }

                $entityManager->persist($post);
                $entityManager->flush();

                $this->addFlash('success', 'Ton article a bien été ajouté !');

                return $this->redirectToRoute('app_home');
            } else {
                $this->addFlash('login', 'Vous devez être connecté pour écrire un article.');
            }
        }

        return $this->render('post/edit/edit_post.html.twig', [
            'post' => $post,
            'pictureList' => $pictureList,
            'videoUrlList' => $urlModifiedList,
            'mediaList' => $videoUrlList,
            'commentList' => $commentList,
            'form' => $postForm,
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
            $urlModifiedList [] = str_replace('youtu.be', 'youtube.com/embed', $url->getVideoUrl());
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
