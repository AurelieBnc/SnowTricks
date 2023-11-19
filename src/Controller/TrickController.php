<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Trick;
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
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/trick')]
class TrickController extends AbstractController
{
    #[Route('/create', name: 'trick_create')]
    public function createTrick(Request $request, EntityManagerInterface $entityManager, PictureService $pictureService, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();
        $trick = new Trick;

        $trickForm = $this->createForm(TrickFormType::class, $trick);
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

                $trick->setCreatedAt(new \DateTimeImmutable());
                $trick->setSlug(strtolower($slugger->slug($trick->getTitle())));
                
                $pictureList = $trickForm->get('pictureList')->getData();
                
                foreach ($pictureList as $picture) {
                    $folder = 'trickImages';
                    $field = $pictureService->add($picture, $folder);

                    $picture = new Picture;
                    $picture->setName($field);
                    $trick->addPicture($picture);
                }

                if (null !== $trickForm->get('media')->getData()) {
                    $media = new Media;
                    $url = $trickForm->get('media')->getData();
                    $media->setVideoUrl($url);
                    $trick->addMedia($media);
                }

                $entityManager->persist($trick);
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

    #[Route('/commentList/{slug}/{loader}', name: 'complete_comment_list')]
    public function commentListRedirect(string $slug, int $loader): Response
    {
        return $this->redirectToRoute('trick', [
            'slug' => $slug,
            'loader' => 0
        ]);
    }

    #[Route('/{slug}/edit/headerImage', name: 'trick_edit_header_image')]
    public function editHeaderImage(string $slug, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $trickRepo = $entityManager->getRepository(Trick::class);
        $trick = $trickRepo->findOneBy(['slug' => $slug]);
        $headerImageForm = $this->createForm(HeaderImageType::class, $trick);

        if ($user) {
            $headerImageForm->handleRequest($request);

            if ($headerImageForm->isSubmitted() && $headerImageForm->isValid()) {
                if ($user->isVerified() === false) {
                    $this->addFlash('verification', 'Vous devez confirmer votre adresse email.');
                    $this->redirectToRoute('app_home');
                }
                // permet de récupérer le nom de l'image
                $trick->setHeaderImage($headerImageForm->get('headerImage')->getData());
                $entityManager->flush();

                return $this->redirectToRoute('trick_edit', [
                    'slug' => $slug,
                ]);
            }
        } else {
            $this->addFlash('login', 'Vous devez être connecté pour modifier le trick.');
        }
        
        return $this->render('trick/edit/edit_header_image.html.twig', [
            'headerImageForm' => $headerImageForm->createView(),
            'headerImage' => $trick->getHeaderImage(),
        ]);
    }


    #[Route('/{slug}/edit/picture/{id}', name: 'trick_edit_picture')]
    public function editPicture(string $slug, int $id, Request $request, EntityManagerInterface $entityManager, PictureService $pictureService ): Response
    {
        $user = $this->getUser();

        $trickRepo = $entityManager->getRepository(Trick::class);
        $trick = $trickRepo->findOneBy(['slug' => $slug]);
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
                $folder = 'trickImages';
                $field = $pictureService->add($picturedata, $folder);
                $pictureService->delete($picture->getName(), $folder);

                $picture->setName($field);
                $picture->setTrick($trick);
                $trick->addPicture($picture);

                $entityManager->persist($picture);
                $entityManager->flush();

                return $this->redirectToRoute('trick_edit', [
                    'slug' => $slug,
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

    #[Route('/{slug}/edit/media/{id}', name: 'trick_edit_media')]
    public function editMedia(string $slug, int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        //todo flusher l'url media au début pour éviter de la modifier a chaque display
        $user = $this->getUser();

        $trickRepo = $entityManager->getRepository(Trick::class);
        $trick = $trickRepo->findOneBy(['slug' => $slug]);        $mediaRepo = $entityManager->getRepository(Media::class);
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

                $media->setTrick($trick);
                $trick->addMedia($media);

                $entityManager->persist($media);
                $entityManager->flush();

                return $this->redirectToRoute('trick_edit', [
                    'slug' => $slug,
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

    #[Route('/{slug}/edit', name: 'trick_edit')]
    public function editTrick(string $slug, Request $request, EntityManagerInterface $entityManager, PictureService $pictureService, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();

        $pictureList = null;
        $videoUrlList = null;
        $commentList = null;

        $trickRepo = $entityManager->getRepository(Trick::class);
        $trick = $trickRepo->findOneBy(['slug' => $slug]);

        $mediaRepo = $entityManager->getRepository(Media::class);
        $videoUrlList = $mediaRepo->videoUrlList($trick->getId());

        $pictureRepo = $entityManager->getRepository(Picture::class);
        $pictureList = $pictureRepo->pictureList($trick->getId()); 

        // Video url display processing
        $urlModifiedList = [];
        foreach ($videoUrlList as $url) {
            
            $modifyUrl = str_replace('youtu.be', 'youtube.com/embed', $url->getVideoUrl());
            $url->setVideoUrl($modifyUrl);
        }

        $trickForm = $this->createForm(TrickFormType::class, $trick);
        if ($user) {
            if ($user->isVerified() === false) {
                $this->addFlash('verification', 'Vous devez confirmer votre adresse email.');
                $this->redirectToRoute('app_home', [
                ]);
            }
            // if ($this->getUser()->is_granted('ROLE_ADMIN') === false) {
            //     $this->addFlash('verification', 'Vous devez être administrateur pour créer un article.');
            //     $this->redirectToRoute('login');
            // }

            $trickForm->handleRequest($request);
            if ($trickForm->isSubmitted() && $trickForm->isValid()) {
                $trick->setCreatedAt(new \DateTimeImmutable());
                
                $trick->setSlug(strtolower($slugger->slug($trick->getTitle())));
                $pictureList = $trickForm->get('pictureList')->getData();
                
                foreach ($pictureList as $picture) {
                    $folder = 'trickImages';
                    $field = $pictureService->add($picture, $folder);

                    $picture = new Picture;
                    $picture->setName($field);
                    $trick->addPicture($picture);
                }

                if (null !== $trickForm->get('media')->getData()) {
                    $media = new Media;
                    $url = $trickForm->get('media')->getData();
                    $media->setVideoUrl($url);
                    $trick->addMedia($media);
                }

                $entityManager->persist($trick);
                $entityManager->flush();

                $this->addFlash('success', 'Ton trick a bien été modifié !');

                return $this->redirectToRoute('trick_edit', [
                    'slug' => $trick->getSlug(),
                ]);
            } 
        } else {
            $this->addFlash('login', 'Vous devez être connecté pour modifier un trick.');
        }

        return $this->render('trick/edit/edit_trick.html.twig', [
            'trick' => $trick,
            'pictureList' => $pictureList,
            'videoUrlList' => $urlModifiedList,
            'mediaList' => $videoUrlList,
            'commentList' => $commentList,
            'form' => $trickForm,
        ]);
    }

    #[Route('/{slug}/{loader}', name: 'trick')]
    public function index(string $slug, int $loader, EntityManagerInterface $entityManager, Request $request, ?UserInterface $user): Response
    {
        $pictureList = null;
        $videoUrlList = null;
        $commentList = null;

        $trickRepo = $entityManager->getRepository(Trick::class);
        $trick = $trickRepo->findOneBy(['slug' => $slug]);

        $mediaRepo = $entityManager->getRepository(Media::class);
        $videoUrlList = $mediaRepo->videoUrlList($trick->getId());

        $pictureRepo = $entityManager->getRepository(Picture::class);
        $pictureList = $pictureRepo->pictureList($trick->getId()); 

        $commentRepo = $entityManager->getRepository(Comment::class);
        $countCommentList = $commentRepo->countCommentList($trick->getId());

        if ($loader === 1) {
            $loader = ($countCommentList > 10) ? 1 : 0;
        }

        if ($loader === 0) {
            $commentList = $commentRepo->commentList($trick->getId());
        } else {   
            $commentList = $commentRepo->commentListMaxTen($trick->getId());
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
                        'slug' => $slug,
                        'loader' => $loader
                    ]);
                }
                $comment->setCreatedAt(new \DateTimeImmutable())
                    ->setTrick($trick)
                    ->setUser($user);
                $entityManager->persist($comment);
                $entityManager->flush();

                $this->addFlash('success', 'Ton commentaire a bien été envoyé !');

                return $this->redirectToRoute('trick', [
                    'slug' => $slug,
                    'loader' => $loader
                ]);
            } 
        } else {
            $this->addFlash('login', 'Vous devez être connecté pour envoyer un commentaire.');
        }

        return $this->render('trick/trick.html.twig', [
            'trick' => $trick,
            'pictureList' => $pictureList,
            'videoUrlList' => $urlModifiedList,
            'commentList' => $commentList,
            'loader' => $loader,
            'commentForm' => $commentForm->createView()
        ]);
    }
}
