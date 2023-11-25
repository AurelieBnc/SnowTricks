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
use Symfony\Component\HttpFoundation\RedirectResponse;
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
                //     $this->addFlash('verification', 'Tu dois être administrateur pour créer un article.');
                //     $this->redirectToRoute('login');
                // }
                if ($user->isVerified() === false) {
                    $this->addFlash('verification', 'Tu dois confirmer ton adresse email.');
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
            $this->addFlash('login', 'Tu dois être connecté pour écrire un nouveau Trick.');
        }

        return $this->render('trick/create_trick.html.twig', [
            'trickForm' => $trickForm,
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

            if ($trick->getHeaderImage() === null) {
                $this->addFlash('info', 'Vous n\'avez pas d\'image d\'en-tête actuellement');
            }
            if ($headerImageForm->isSubmitted() && $headerImageForm->isValid()) {
                if ($user->isVerified() === false) {
                    $this->addFlash('verification', 'Tu dois confirmer ton adresse email.');
                    $this->redirectToRoute('app_home');
                }
                // permet de récupérer le nom de l'image
                $trick->setHeaderImage($headerImageForm->get('headerImage')->getData());
                $entityManager->flush();

                $this->addFlash('success', 'Ton image d\'en-tête a bien été modifiée.');

                return $this->redirectToRoute('trick_edit', [
                    'slug' => $slug,
                ]);
            }
        } else {
            $this->addFlash('login', 'Tu dois être connecté pour effectuer une modification.');
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
                    $this->addFlash('verification', 'Tu dois confirmer ton adresse email.');
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
            $this->addFlash('login', 'Tu dois être connecté pour modifier le trick.');
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
        $trick = $trickRepo->findOneBy(['slug' => $slug]);  

        $mediaRepo = $entityManager->getRepository(Media::class);
        $media = $mediaRepo->find($id);

        $editMedia = new Media;

        $mediaForm = $this->createForm(MediaType::class, $editMedia);
        if ($user) {
            $mediaForm->handleRequest($request);
            if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {

                if ($user->isVerified() === false) {
                    $this->addFlash('verification', 'Tu dois confirmer ton adresse email.');
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
            $this->addFlash('login', 'Tu dois être connecté pour modifier un lien.');
        }

        $modifyUrl = str_replace('youtu.be', 'youtube.com/embed', $media->getVideoUrl());
        $media->setVideoUrl($modifyUrl);

        return $this->render('trick/edit/edit_media.html.twig', [
            'form' => $mediaForm,
            'media' => $media,
        ]);

    }

    #[Route('/{slug}/edit', name: 'trick_edit')]
    public function editTrick(Trick $trick, Request $request, EntityManagerInterface $entityManager, PictureService $pictureService, SluggerInterface $slugger): Response
    {
        $user = $this->getUser();

        $pictureList = null;
        $videoUrlList = null;

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
                $this->addFlash('verification', 'Tu dois confirmer ton adresse email.');
                $this->redirectToRoute('app_home', [
                ]);
            }
            // if ($this->getUser()->is_granted('ROLE_ADMIN') === false) {
            //     $this->addFlash('verification', 'Tu dois être administrateur pour créer un article.');
            //     $this->redirectToRoute('login');
            // }

            $trickForm->handleRequest($request);
            if ($trickForm->isSubmitted() && $trickForm->isValid()) {
                if ($trickForm->get('delete')->isClicked()) {
                    //clean Trick before delete
                    $commentList = null;
            
                    $commentRepo = $entityManager->getRepository(Comment::class);
                    $commentList = $commentRepo->commentList($trick->getId());

                    if (!empty($videoUrlList)) {
                        foreach ($videoUrlList as $url) {
                            $entityManager->remove($url);
                        }
                    }
                    if (!empty($pictureList)) {
                        foreach ($pictureList as $picture) {
                            $entityManager->remove($picture);
                        }
                    }
                    if (!empty($commentList)) {
                        foreach ($commentList as $comment) {
                            $entityManager->remove($comment);
                        }
                    }

                    $entityManager->remove($trick);
                    $entityManager->flush();
    
                    $this->addFlash('success', 'Le trick a bien été supprimé, ainsi que tous ses médias et commentaires.');

                    return $this->redirectToRoute('app_home');
                }

                $trick->setUpdateDate(new \DateTimeImmutable());
                
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
            $this->addFlash('login', 'Tu dois être connecté pour modifier un trick.');
        }

        return $this->render('trick/edit/edit_trick.html.twig', [
            'trick' => $trick,
            'pictureList' => $pictureList,
            'videoUrlList' => $urlModifiedList,
            'mediaList' => $videoUrlList,
            'form' => $trickForm,
        ]);
    }

    #[Route('/{slug}/delete/headerImage', name: 'delete_header_image')]
    public function deleteHeaderImage(Trick $trick, EntityManagerInterface $entityManager): RedirectResponse
    {
        $user = $this->getUser();

        if ($user) {
            if ($user->isVerified() === false) {
                $this->addFlash('verification', 'Tu dois confirmer ton adresse email.');
                $this->redirectToRoute('app_home', [
                ]);
            }
            if ($trick->getHeaderImage()) {
                $trick->setHeaderImage(null);

                $entityManager->persist($trick);
                $entityManager->flush();

                 $this->addFlash('success', 'Ton image d\'en-tête a bien été supprimée.'); 
            } else {
                $this->addFlash('error', 'Vous n\'avez pas d\'image d\'en-tête actuellement.');
            }

        }  else {
            $this->addFlash('login', 'Tu dois être connecté pour modifier un trick.');
        }

        return $this->redirectToRoute('trick_edit', [
            'slug' => $trick->getSlug(),
        ]);
    }
    
    #[Route('/{slug}/delete/picture/{id}', name: 'delete_picture')]
    public function deletePicture(string $slug, int $id, EntityManagerInterface $entityManager, PictureService $pictureService): RedirectResponse
    {
        $user = $this->getUser();

        $trickRepo = $entityManager->getRepository(Trick::class);
        $trick = $trickRepo->findOneBy(['slug' => $slug]);

        $pictureRepo = $entityManager->getRepository(Picture::class);
        $deletePicture = $pictureRepo->find($id);

        if ($user) {
            if ($user->isVerified() === false) {
                $this->addFlash('verification', 'Tu dois confirmer ton adresse email.');
                $this->redirectToRoute('app_home', [
                ]);
            }
            if (isset($deletePicture)) {
                if ($deletePicture->getName() === $trick->getHeaderImage()) {
                    $trick->setHeaderImage(null);
                }
                $entityManager->remove($deletePicture);
                $entityManager->flush();

                $folder = 'trickImages';
                $pictureService->delete($deletePicture->getName(), $folder);

                 $this->addFlash('success', 'Ton image a bien été supprimée.'); 
            } else {
                $this->addFlash('error', 'Cette image n\a pas été retrouvée.');
            }
        } else {
            $this->addFlash('login', 'Tu dois être connecté pour modifier un trick.');
        }

        return $this->redirectToRoute('trick_edit', [
            'slug' => $slug,
        ]);
    }

    #[Route('/{slug}/delete/media/{id}', name: 'delete_media')]
    public function deleteMedia(string $slug, int $id, EntityManagerInterface $entityManager): RedirectResponse
    {
        $user = $this->getUser();

        $mediaRepo = $entityManager->getRepository(Media::class);        
        $media = $mediaRepo->find($id);

        if ($user) {
            if ($user->isVerified() === false) {
                $this->addFlash('verification', 'Tu dois confirmer ton adresse email.');
                $this->redirectToRoute('app_home', [
                ]);
            }
            if ($media) {
                $entityManager->remove($media);
                $entityManager->flush();

                 $this->addFlash('success', 'Ton lien Youtube a bien été supprimé.'); 
            } else {
                $this->addFlash('error', 'Le lien n\'a pas été trouvé.');
            }

        }  else {
            $this->addFlash('login', 'Tu dois être connecté pour modifier un trick.');
        }

        return $this->redirectToRoute('trick_edit', [
            'slug' => $slug,
        ]);
    }

    #[Route('/{slug}/delete', name: 'delete_trick')]
    public function deleteTrick(Trick $trick, EntityManagerInterface $entityManager): RedirectResponse
    {
        $user = $this->getUser();

        if ($user) {
            if ($user->isVerified() === false) {
                $this->addFlash('verification', 'Tu dois confirmer ton adresse email.');
                $this->redirectToRoute('app_home', [
                ]);
            }

            //clean Trick before delete
            $pictureList = null;
            $videoUrlList = null;
            $commentList = null;
    
            $mediaRepo = $entityManager->getRepository(Media::class);
            $videoUrlList = $mediaRepo->videoUrlList($trick->getId());
    
            $pictureRepo = $entityManager->getRepository(Picture::class);
            $pictureList = $pictureRepo->pictureList($trick->getId()); 
    
            $commentRepo = $entityManager->getRepository(Comment::class);
            $commentList = $commentRepo->commentList($trick->getId());

            if (!empty($videoUrlList)) {
                foreach ($videoUrlList as $url) {
                    $entityManager->remove($url);
                }
            }
            if (!empty($pictureList)) {
                foreach ($pictureList as $picture) {
                    $entityManager->remove($picture);
                }
            }
            if (!empty($commentList)) {
                foreach ($commentList as $comment) {
                    $entityManager->remove($comment);
                }
            }
            
            $entityManager->remove($trick);
            $entityManager->flush();

            $this->addFlash('success', 'Le trick a bien été supprimé, ainsi que tous ses médias et commentaires.');
        } else {
            $this->addFlash('login', 'Tu dois être connecté pour supprimer un trick.');
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/{slug}', name: 'trick')]
    public function index(string $slug, EntityManagerInterface $entityManager, Request $request, ?UserInterface $user): Response
    {
        $pictureList = null;
        $videoUrlList = null;
        $commentlistPaginated = null;
        $page = $request->query->getInt('page', 1);

        $trickRepo = $entityManager->getRepository(Trick::class);
        $trick = $trickRepo->findOneBy(['slug' => $slug]);

        $mediaRepo = $entityManager->getRepository(Media::class);
        $videoUrlList = $mediaRepo->videoUrlList($trick->getId());

        $pictureRepo = $entityManager->getRepository(Picture::class);
        $pictureList = $pictureRepo->pictureList($trick->getId()); 

        $commentRepo = $entityManager->getRepository(Comment::class);
        $commentlistPaginated = $commentRepo->findCommentListPaginated($page, $trick->getId());

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
                    $this->addFlash('verification', 'Tu dois confirmer ton adresse email.');
                    $this->redirectToRoute('trick', [
                        'slug' => $slug,
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
                ]);
            } 
        } else {
            $this->addFlash('login', 'Tu dois être connecté pour envoyer un commentaire.');
        }

        return $this->render('trick/trick.html.twig', [
            'trick' => $trick,
            'pictureList' => $pictureList,
            'videoUrlList' => $urlModifiedList,
            'commentList' => $commentlistPaginated,
            'commentForm' => $commentForm->createView()
        ]);
    }
}
