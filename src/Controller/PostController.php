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
use App\Form\TrickFormType;
use App\Form\MediaType;
use App\Form\PictureType;
use App\Form\HeaderImageType;
use App\Service\PictureService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/trick')]
class PostController extends AbstractController
{
    #[Route('/create', name: 'trick_create')]
    public function createTrick(Request $request, EntityManagerInterface $entityManager, PictureService $pictureService): Response
    {
        $user = $this->getUser();
        $post = new Post;

        $trickForm = $this->createForm(TrickFormType::class, $post);
        if ($user) {
            $trickForm->handleRequest($request);

            if ($trickForm->isSubmitted() && $trickForm->isValid()) {

                // if ($this->getUser()->is_granted('ROLE_ADMIN') === false) {
                //     $this->addFlash('verification', 'Vous devez être administrateur pour créer un article.');
                //     $this->redirectToRoute('login');
                // }
                if ($user->isVerified() === false) {
                    $this->addFlash('verification', 'Vous devez confirmer votre adresse email.');
                    $this->redirectToRoute('app_home', [
                    ]);
                }

                $post->setCreatedAt(new \DateTimeImmutable()); 
                
                $pictureList = $trickForm->get('pictureList')->getData();
                
                foreach ($pictureList as $picture) {
                    $folder = 'postImages';
                    $field = $pictureService->add($picture, $folder);

                    $picture = new Picture;
                    $picture->setName($field);
                    $post->addPicture($picture);
                }

                if (null !== $trickForm->get('media')->getData()) {
                    $media = new Media;
                    $url = $trickForm->get('media')->getData();
                    $media->setVideoUrl($url);
                    $post->addMedia($media);
                }

                $entityManager->persist($post);
                $entityManager->flush();

                $this->addFlash('success', 'Ton trick a bien été ajouté !');

                return $this->redirectToRoute('app_home');
            } 
        } else {
            $this->addFlash('login', 'Vous devez être connecté pour écrire un nouveau Trick.');
        }

        return $this->render('trick/create_trick.html.twig', [
            'trickForm' => $trickForm,
        ]);
    }

    #[Route('/{trick_id}/edit/headerImage', name: 'trick_edit_header_image')]
    public function editHeaderImage(int $trick_id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $postRepo = $entityManager->getRepository(Post::class);
        $post = $postRepo->find($trick_id); 

        $headerImageForm = $this->createForm(HeaderImageType::class, $post);

        if ($user) {
            $headerImageForm->handleRequest($request);

            if ($headerImageForm->isSubmitted() && $headerImageForm->isValid()) {
                if ($user->isVerified() === false) {
                    $this->addFlash('verification', 'Vous devez confirmer votre adresse email.');
                    $this->redirectToRoute('app_home');
                }
                // permet de récupérer le nom de l'image
                $post->setHeaderImage($headerImageForm->get('headerImage')->getData());
                $entityManager->flush();

                return $this->redirectToRoute('trick_edit', [
                    'id' => $post->getId(),
                ]);
            }
        } else {
            $this->addFlash('login', 'Vous devez être connecté pour modifier le trick.');
        }
        
        return $this->render('trick/edit/edit_header_image.html.twig', [
            'headerImageForm' => $headerImageForm->createView(),
            'headerImage' => $post->getHeaderImage(),
        ]);
    }


    #[Route('/{trick_id}/edit/picture/{id}', name: 'trick_edit_picture')]
    public function editPicture(int $trick_id, int $id, Request $request, EntityManagerInterface $entityManager, PictureService $pictureService ): Response
    {
        $user = $this->getUser();

        $postRepo = $entityManager->getRepository(Post::class);
        $post = $postRepo->find($trick_id);

        $pictureRepo = $entityManager->getRepository(Picture::class);
        $picture = $pictureRepo->find($id);

        $editPicture = new Picture;

        $pictureForm = $this->createForm(PictureType::class, $editPicture);
        if ($user) {     
            $pictureForm->handleRequest($request);
            if ($pictureForm->isSubmitted() && $pictureForm->isValid()) {
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

                return $this->redirectToRoute('trick_edit', [
                    'id' => $post->getId(),
                ]);
            } 
        } else {
            $this->addFlash('login', 'Vous devez être connecté pour modifier le trick.');
        }

        return $this->render('trick/edit/edit_picture.html.twig', [
            'form' => $pictureForm,
            'picture' => $picture,
        ]);
    }

    #[Route('/{trick_id}/edit/media/{id}', name: 'trick_edit_media')]
    public function editMedia(int $trick_id, int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        //todo flusher l'url media au début pour éviter de la modifier a chaque display
        $user = $this->getUser();

        $postRepo = $entityManager->getRepository(Post::class);
        $post = $postRepo->find($trick_id);

        $mediaRepo = $entityManager->getRepository(Media::class);
        $media = $mediaRepo->find($id);

        $editMedia = new Media;

        $mediaForm = $this->createForm(MediaType::class, $editMedia);
        if ($user) {
            $mediaForm->handleRequest($request);
            if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {

                if ($user->isVerified() === false) {
                    $this->addFlash('verification', 'Vous devez confirmer votre adresse email.');
                    $this->redirectToRoute('app_home', [
                    ]);
                }

                $modifyUrl = str_replace('youtu.be', 'youtube.com/embed', $editMedia->getVideoUrl());
                $media->setVideoUrl($modifyUrl); 

                $media->setPost($post);
                $post->addMedia($media);

                $entityManager->persist($media);
                $entityManager->flush();

                return $this->redirectToRoute('trick_edit', [
                    'id' => $post->getId(),
                ]);
            } 
        } else {
            $this->addFlash('login', 'Vous devez être connecté pour modifier un lien.');
        }

        $modifyUrl = str_replace('youtu.be', 'youtube.com/embed', $media->getVideoUrl());
        $media->setVideoUrl($modifyUrl);

        return $this->render('trick/edit/edit_media.html.twig', [
            'form' => $mediaForm,
            'media' => $media,
        ]);

    }

    #[Route('/{id}/edit', name: 'trick_edit')]
    public function editTrick(int $id, Request $request, EntityManagerInterface $entityManager, PictureService $pictureService): Response
    {
        $user = $this->getUser();

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

        $trickForm = $this->createForm(TrickFormType::class, $post);
        if ($user) {
            $trickForm->handleRequest($request);
            if ($trickForm->isSubmitted() && $trickForm->isValid()) {
                // if ($this->getUser()->is_granted('ROLE_ADMIN') === false) {
                //     $this->addFlash('verification', 'Vous devez être administrateur pour créer un article.');
                //     $this->redirectToRoute('login');
                // }
                if ($user->isVerified() === false) {
                    $this->addFlash('verification', 'Vous devez confirmer votre adresse email.');
                    $this->redirectToRoute('app_home', [
                    ]);
                }

                $post->setCreatedAt(new \DateTimeImmutable()); 
                
                $pictureList = $trickForm->get('pictureList')->getData();
                
                foreach ($pictureList as $picture) {
                    $folder = 'postImages';
                    $field = $pictureService->add($picture, $folder);

                    $picture = new Picture;
                    $picture->setName($field);
                    $post->addPicture($picture);
                }

                if (null !== $trickForm->get('media')->getData()) {
                    $media = new Media;
                    $url = $trickForm->get('media')->getData();
                    $media->setVideoUrl($url);
                    $post->addMedia($media);
                }

                $entityManager->persist($post);
                $entityManager->flush();

                $this->addFlash('success', 'Ton trick a bien été modifié !');

                return $this->redirectToRoute('trick_edit', [
                    'id' => $post->getId(),
                ]);
            } 
        } else {
            $this->addFlash('login', 'Vous devez être connecté pour modifier un trick.');
        }

        return $this->render('trick/edit/edit_trick.html.twig', [
            'post' => $post,
            'pictureList' => $pictureList,
            'videoUrlList' => $urlModifiedList,
            'mediaList' => $videoUrlList,
            'commentList' => $commentList,
            'form' => $trickForm,
        ]);
    }

    #[Route('/{id}/{loader}', name: 'trick')]
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
        if ($user) {
            $commentForm->handleRequest($request);
            if ($commentForm->isSubmitted() && $commentForm->isValid()) {

                if ($user->isVerified() === false) {
                    $this->addFlash('verification', 'Vous devez confirmer votre adresse email.');
                    $this->redirectToRoute('trick', [
                        'id' => $post->getId(),
                        'loader' => $loader
                    ]);
                }
                $comment->setCreatedAt(new \DateTimeImmutable())
                    ->setPost($post)
                    ->setUser($user);
                $entityManager->persist($comment);
                $entityManager->flush();

                $this->addFlash('success', 'Ton commentaire a bien été envoyé !');

                return $this->redirectToRoute('trick', [
                    'id' => $post->getId(),
                    'loader' => $loader
                ]);
            } 
        } else {
            $this->addFlash('login', 'Vous devez être connecté pour envoyer un commentaire.');
        }

        return $this->render('trick/trick.html.twig', [
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
        return $this->redirectToRoute('trick', [
            'id' => $post,
            'loader' => 0
        ]);
    }
}
