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
use App\Form\TrickFormType;
use App\Form\MediaType;
use App\Form\PictureType;
use App\Form\HeaderImageType;
use App\Service\CommentService;
use App\Service\LinkYoutubeService;
use App\Service\PictureService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/trick')]
class TrickController extends AbstractController
{
    const NAME_FOLDER_PICTURE = 'trickImages';

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/create', name: 'trick_create')]
    public function createTrick(Request $request, EntityManagerInterface $entityManager, PictureService $pictureService,LinkYoutubeService $linkYoutubeService, SluggerInterface $slugger): Response
    {
        $trick = new Trick;

        $trickForm = $this->createForm(TrickFormType::class, $trick);
        $trickForm->handleRequest($request);

        if ($trickForm->isSubmitted() && $trickForm->isValid()) {
            
            $pictureList = $trickForm->get('pictureList')->getData();
            
            foreach ($pictureList as $picture) {
                $newNamePicture = $pictureService->add($picture, SELF::NAME_FOLDER_PICTURE);
                $newPicture = new Picture;

                $newPicture->setName($newNamePicture);
                $trick->addPicture($newPicture);
            }

            $url = $trickForm->get('media')->getData();

            if (null !== $url) {
                $media = new Media;
                $urlModified = $linkYoutubeService->intoEmbedLinkYoutbe($url);

                $media->setVideoUrl($urlModified);
                $trick->addMedia($media);
            }

            $entityManager->persist($trick);
            $trick->setHeaderImage(
                $trick->getPictureList()[0]?->getName() ? $trick->getPictureList()[0]->getName() : null
            );

            $entityManager->flush();

            $this->addFlash('success', 'Ton trick a bien été ajouté !');

            return $this->redirectToRoute('app_home');
        } 

        return $this->render('trick/create_trick.html.twig', [
            'trickForm' => $trickForm->createView(),
        ]);
    }

    #[IsGranted('HEADER_IMAGE_EDIT', 'trick')]
    #[Route('/{slug}/edit/headerImage', name: 'trick_edit_header_image')]
    public function editHeaderImage(Trick $trick, Request $request, EntityManagerInterface $entityManager): Response
    {
        $headerImageForm = $this->createForm(HeaderImageType::class, $trick);

        $headerImageForm->handleRequest($request);

        if ($trick->getHeaderImage() === null) {
            $this->addFlash('info', 'Vous n\'avez pas d\'image d\'en-tête actuellement');
        }
        if ($headerImageForm->isSubmitted() && $headerImageForm->isValid()) {
            $trick->setHeaderImage($headerImageForm->get('headerImage')->getData());
            $entityManager->flush();

            $this->addFlash('success', 'Ton image d\'en-tête a bien été modifiée.');

            return $this->redirectToRoute('trick_edit', [
                'slug' => $trick->getSlug(),
            ]);
        }
        
        return $this->render('trick/edit/edit_header_image.html.twig', [
            'headerImageForm' => $headerImageForm->createView(),
            'headerImage' => $trick->getHeaderImage(),
        ]);
    }

    #[Route('/{slug}/edit/picture/{pictureId}', name: 'trick_edit_picture')]
    public function editPicture(string $slug, int $pictureId, Request $request, EntityManagerInterface $entityManager, PictureService $pictureService ): Response
    {
        $trickRepo = $entityManager->getRepository(Trick::class);
        $trick = $trickRepo->findOneBy(['slug' => $slug]);
        $pictureRepo = $entityManager->getRepository(Picture::class);
        $picture = $pictureRepo->find($pictureId);

        $this->denyAccessUnlessGranted('PICTURE_EDIT',$picture);

        $editPicture = new Picture;
        $pictureForm = $this->createForm(PictureType::class, $editPicture);
  
        $pictureForm->handleRequest($request);
        if ($pictureForm->isSubmitted() && $pictureForm->isValid()) {
            $picturedata = $pictureForm->get('name')->getData();
            $newNamePicture = $pictureService->add($picturedata, SELF::NAME_FOLDER_PICTURE);
            $pictureService->delete($picture->getName(), SELF::NAME_FOLDER_PICTURE);

            $picture->setName($newNamePicture); 
            $picture->setTrick($trick);

            $entityManager->persist($picture);
            $entityManager->flush();

            return $this->redirectToRoute('trick_edit', [
                'slug' => $slug,
            ]);
        } 

        return $this->render('trick/edit/edit_picture.html.twig', [
            'form' => $pictureForm->createView(),
            'picture' => $picture,
        ]);
    }

    #[Route('/{slug}/edit/media/{mediaId}', name: 'trick_edit_media')]
    public function editMedia(string $slug, int $mediaId, Request $request, EntityManagerInterface $entityManager, LinkYoutubeService $linkYoutubeService): Response
    {
        $mediaRepo = $entityManager->getRepository(Media::class);
        $media = $mediaRepo->find($mediaId);

        $this->denyAccessUnlessGranted('MEDIA_EDIT', $media);

        $mediaForm = $this->createForm(MediaType::class, $media);
        $mediaForm->handleRequest($request);

        if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {

            //utiliser les data transformer de symfony
            $urlModified = $linkYoutubeService->intoEmbedLinkYoutbe($media->getVideoUrl());
            $media->setVideoUrl($urlModified); 

            $entityManager->persist($media);
            $entityManager->flush();

            $this->addFlash('success', 'Le lien a bien été modifié.');

            return $this->redirectToRoute('trick_edit', [
                'slug' => $slug,
            ]);
        } 

        return $this->render('trick/edit/edit_media.html.twig', [
            'form' => $mediaForm->createView(),
            'media' => $media,
        ]);

    }

    #[IsGranted('TRICK_EDIT', 'trick')]
    #[Route('/{slug}/edit', name: 'trick_edit')]
    public function editTrick(Trick $trick, Request $request, EntityManagerInterface $entityManager, PictureService $pictureService, LinkYoutubeService $linkYoutubeService, SluggerInterface $slugger): Response
    {
        $trickForm = $this->createForm(TrickFormType::class, $trick);
        $trickForm->handleRequest($request);

        if ($trickForm->isSubmitted() && $trickForm->isValid()) {

            if ($trickForm->get('delete')->isClicked()) {
                $entityManager->remove($trick);
                $entityManager->flush();

                $this->addFlash('success', 'Le trick a bien été supprimé, ainsi que tous ses médias et commentaires.');

                return $this->redirectToRoute('app_home');
            }
            
            $pictureList = $trickForm->get('pictureList')->getData();
            
            foreach ($pictureList as $picture) {               
                $newNamePicture = $pictureService->add($picture, SELF::NAME_FOLDER_PICTURE);
                $picture = new Picture;
                $picture->setName($newNamePicture);
                $trick->addPicture($picture);
            }

            $url = $trickForm->get('media')->getData();
            if (null !== $url) {
                $media = new Media;
                $urlModified = $linkYoutubeService->intoEmbedLinkYoutbe($url);

                $media->setVideoUrl($urlModified);
                $trick->addMedia($media);
            }

            $entityManager->persist($trick);

            if ($trick->getHeaderImage()=== null) {
                $trick->setHeaderImage(
                    $trick->getPictureList()[0]?->getName() ? $trick->getPictureList()[0]->getName() : null
                );
            }

            $entityManager->flush();

            $this->addFlash('success', 'Ton trick a bien été modifié !');

            return $this->redirectToRoute('trick_edit', [
                'slug' => $trick->getSlug(),
            ]);
        } 

        return $this->render('trick/edit/edit_trick.html.twig', [
            'trick' => $trick,
            'form' => $trickForm->createView(),
        ]);
    }

    #[IsGranted('HEADER_IMAGE_DELETE', 'trick')]
    #[Route('/{slug}/delete/headerImage', name: 'delete_header_image')]
    public function deleteHeaderImage(Trick $trick, EntityManagerInterface $entityManager): RedirectResponse
    {
         if ($trick->getHeaderImage()) {
            $trick->setHeaderImage(null);

            $entityManager->persist($trick);
            $entityManager->flush();

                $this->addFlash('success', 'Ton image d\'en-tête a bien été supprimée.'); 
        } else {
            $this->addFlash('error', 'Vous n\'avez pas d\'image d\'en-tête actuellement.');
        }

        return $this->redirectToRoute('trick_edit', [
            'slug' => $trick->getSlug(),
        ]);
    }
    
    #[Route('/{slug}/delete/picture/{pictureId}', name: 'delete_picture')]
    public function deletePicture(string $slug, int $pictureId, EntityManagerInterface $entityManager, PictureService $pictureService): RedirectResponse
    {
        $trickRepo = $entityManager->getRepository(Trick::class);
        $trick = $trickRepo->findOneBy(['slug' => $slug]);

        $pictureRepo = $entityManager->getRepository(Picture::class);
        $deletePicture = $pictureRepo->find($pictureId);

        $this->denyAccessUnlessGranted('PICTURE_DELETE',$deletePicture);

        if (isset($deletePicture)) {
            if ($deletePicture->getName() === $trick->getHeaderImage()) {
                $trick->setHeaderImage(null);
            }
            $entityManager->remove($deletePicture);
            $entityManager->flush();

            $pictureService->delete($deletePicture->getName(), SELF::NAME_FOLDER_PICTURE);

            $this->addFlash('success', 'Ton image a bien été supprimée.'); 
        } else {
            $this->addFlash('error', 'Cette image n\'a pas été retrouvée.');
        }

        return $this->redirectToRoute('trick_edit', [
            'slug' => $slug,
        ]);
    }

    #[Route('/{slug}/delete/media/{mediaId}', name: 'delete_media')]
    public function deleteMedia(string $slug, int $mediaId, EntityManagerInterface $entityManager): RedirectResponse
    {
        $mediaRepo = $entityManager->getRepository(Media::class);        
        $media = $mediaRepo->find($mediaId);

        $this->denyAccessUnlessGranted('MEDIA_DELETE',$media);

        if ($media) {
            $entityManager->remove($media);
            $entityManager->flush();

            $this->addFlash('success', 'Ton lien Youtube a bien été supprimé.'); 
        } else {
            $this->addFlash('error', 'Le lien n\'a pas été trouvé.');
        }

        return $this->redirectToRoute('trick_edit', [
            'slug' => $slug,
        ]);
    }

    #[IsGranted('TRICK_DELETE', 'trick')]
    #[Route('/{slug}/delete', name: 'delete_trick')]
    public function deleteTrick(Trick $trick, EntityManagerInterface $entityManager): RedirectResponse
    {
        $entityManager->remove($trick);
        $entityManager->flush();

        $this->addFlash('success', 'Le trick a bien été supprimé, ainsi que tous ses médias et commentaires.');

        return $this->redirectToRoute('app_home');
    }

    #[Route('/{slug}', name: 'trick')]
    public function index(Trick $trick, EntityManagerInterface $entityManager, Request $request, ?UserInterface $user, CommentService $commentService): Response
    {
        $commentlistPaginated = null;

        $page = $request->query->getInt('page', 1);
        if ($page < 1) {
            throw $this->createNotFoundException('Numéro de page invalide');
        }
        $commentForm = $commentService->getCommentForm($request, $user, $trick);

        $commentRepo = $entityManager->getRepository(Comment::class);
        $commentlistPaginated = $commentRepo->findCommentListPaginated($page, $trick->getId());

        return $this->render('trick/trick.html.twig', [
            'trick' => $trick,
            'commentlistPaginated' => $commentlistPaginated,
            'commentForm' => $commentForm->createView()
        ]);
    }
}
