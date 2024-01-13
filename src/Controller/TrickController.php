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
use App\Form\HeaderPictureNameType;
use App\Manager\CommentManager;
use App\Manager\PictureManager;
use App\Manager\MediaManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/trick')]
class TrickController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/create', name: 'trick_create')]
    public function createTrick(Request $request, EntityManagerInterface $entityManager, PictureManager $pictureManager, MediaManager $mediaManager): Response
    {
        $trick = new Trick;

        $trickForm = $this->createForm(TrickFormType::class, $trick);
        $trickForm->handleRequest($request);

        if ($trickForm->isSubmitted() && $trickForm->isValid()) {
            $uploadedPictureFileList = $trickForm->get('pictureList')->getData();
            $pictureManager->addUploadedPictureFileList($uploadedPictureFileList, $trick);

            $url = $trickForm->get('media')->getData();
            if (null !== $url) {
                $mediaManager->add($trick, $url);
            }

            $entityManager->persist($trick);
            $entityManager->flush();

            $this->addFlash('success', 'Ton trick a bien été ajouté !');

            return $this->redirectToRoute('app_home');
        } 

        return $this->render('trick/create_trick.html.twig', [
            'trickForm' => $trickForm->createView(),
        ]);
    }

    #[IsGranted('HEADER_PICTURE_NAME_EDIT', 'trick')]
    #[Route('/{slug}/edit/headerPictureName', name: 'trick_edit_header_picture_name')]
    public function editHeaderPictureName(Trick $trick, Request $request, EntityManagerInterface $entityManager): Response
    {
        $headerPictureNameForm = $this->createForm(HeaderPictureNameType::class, $trick);

        $headerPictureNameForm->handleRequest($request);
        if ($trick->getHeaderPictureName() === null) {
            $this->addFlash('info', 'Vous n\'avez pas d\'image d\'en-tête actuellement');
        }

        if ($headerPictureNameForm->isSubmitted() && $headerPictureNameForm->isValid()) {
            $trick->setHeaderPictureName($headerPictureNameForm->get('headerPictureName')->getData());
            $entityManager->flush();

            $this->addFlash('success', 'Ton image d\'en-tête a bien été modifiée.');

            return $this->redirectToRoute('trick_edit', [
                'slug' => $trick->getSlug(),
            ]);
        }
        
        return $this->render('trick/edit/edit_header_picture_name.html.twig', [
            'headerPictureNameForm' => $headerPictureNameForm->createView(),
            'headerPictureName' => $trick->getHeaderPictureName(),
        ]);
    }

    #[Route('/{slug}/edit/picture/{pictureId}', name: 'trick_edit_picture')]
    public function editPicture(string $slug, int $pictureId, Request $request, EntityManagerInterface $entityManager, PictureManager $pictureManager): Response
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
            $uploadedPictureFile = $pictureForm->get('name')->getData();

            $pictureManager->editUploadedPictureFile($picture, $uploadedPictureFile, $trick);

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
    public function editMedia(string $slug, int $mediaId, Request $request, EntityManagerInterface $entityManager, MediaManager $mediaManager): Response
    {
        $mediaRepo = $entityManager->getRepository(Media::class);
        $media = $mediaRepo->find($mediaId);

        $this->denyAccessUnlessGranted('MEDIA_EDIT', $media);

        $mediaForm = $this->createForm(MediaType::class, $media);
        $mediaForm->handleRequest($request);

        if ($mediaForm->isSubmitted() && $mediaForm->isValid()) {
            if (null !== $media->getVideoUrl()) {
                $mediaManager->edit($media);
            }

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
    public function editTrick(Trick $trick, Request $request, EntityManagerInterface $entityManager, PictureManager $pictureManager, MediaManager $mediaManager): Response
    {
        $trickForm = $this->createForm(TrickFormType::class, $trick);
        $trickForm->handleRequest($request);

        if ($trickForm->isSubmitted() && $trickForm->isValid()) {
            if ($trickForm->get('delete')->isClicked()) {
                $pictureManager->deletePictureFileList($trick);

                $entityManager->remove($trick);
                $entityManager->flush();

                $this->addFlash('success', 'Le trick a bien été supprimé, ainsi que tous ses médias et commentaires.');

                return $this->redirectToRoute('app_home');
            }

            $uploadedPictureFileList = $trickForm->get('pictureList')->getData();
            $pictureManager->addUploadedPictureFileList($uploadedPictureFileList, $trick);

            $url = $trickForm->get('media')->getData();
            if (null !== $url) {
                $mediaManager->add($trick, $url);
            }

            $entityManager->persist($trick);
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

    #[IsGranted('HEADER_PICTURE_NAME_DELETE', 'trick')]
    #[Route('/{slug}/delete/headerPictureName', name: 'delete_header_picture_name')]
    public function deleteHeaderPictureName(Trick $trick, EntityManagerInterface $entityManager): RedirectResponse
    {
         if ($trick->getHeaderPictureName()) {
            $trick->setHeaderPictureName($trick->getPictureList()[0]?->getName() ? $trick->getPictureList()[0]->getName() : null);

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
    public function deletePicture(string $slug, int $pictureId, EntityManagerInterface $entityManager, PictureManager $pictureManager): RedirectResponse
    {
        $trickRepo = $entityManager->getRepository(Trick::class);
        $trick = $trickRepo->findOneBy(['slug' => $slug]);

        $pictureRepo = $entityManager->getRepository(Picture::class);
        $deletePicture = $pictureRepo->find($pictureId);

        $this->denyAccessUnlessGranted('PICTURE_DELETE', $deletePicture);

        if (isset($deletePicture)) {
            if($deletePicture->getName() === $trick->getHeaderPictureName()) {
                $this->addFlash('error', 'Cette image est ton image d\en-tête, tu dois la modifier avant de pouvoir la supprimer');
            }else{
                $pictureManager->deletePictureAndPictureFile($deletePicture, $trick);
                $this->addFlash('success', 'Ton image a bien été supprimée.'); 
            }
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
    public function deleteTrick(Trick $trick, EntityManagerInterface $entityManager, PictureManager $pictureManager): RedirectResponse
    {
        $pictureManager->deletePictureFileList($trick);

        $entityManager->remove($trick);
        $entityManager->flush();

        $this->addFlash('success', 'Le trick a bien été supprimé, ainsi que tous ses médias et commentaires.');

        return $this->redirectToRoute('app_home');
    }

    #[Route('/{slug}', name: 'trick')]
    public function index(Trick $trick, EntityManagerInterface $entityManager, Request $request, ?UserInterface $user, CommentManager $commentManager): Response
    {
        $commentlistPaginated = null;

        $page = $request->query->getInt('page', 1);
        if ($page < 1) {
            throw $this->createNotFoundException('Numéro de page invalide');
        }
        
        $commentForm = $commentManager->getCommentForm($request, $user, $trick);

        $commentRepo = $entityManager->getRepository(Comment::class);
        $commentlistPaginated = $commentRepo->findCommentListPaginated($page, $trick->getId());

        return $this->render('trick/trick.html.twig', [
            'trick' => $trick,
            'commentlistPaginated' => $commentlistPaginated,
            'commentForm' => $commentForm->createView()
        ]);
    }
}
