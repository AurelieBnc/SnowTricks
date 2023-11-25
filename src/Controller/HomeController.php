<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function home(Request $request, EntityManagerInterface $entityManager, ?UserInterface $user): Response
    {
        if ($user) {
            if ($user->isVerified() === false) {
                $this->addFlash('verification', 'Vous devez confirmer votre adresse email.');
                $this->redirectToRoute('app_home');
            }
            if (!$user->isVerified() === false) {
                $this->addFlash('hello', 'Une bonne journée à toi '.$user->getUsername().', Ride On !');
                $this->redirectToRoute('app_home');
            }
        }
        $page = $request->query->getInt('page', 1);

        /** @var App\Repository\TrickRepository $repo */
        $trickRepo = $entityManager->getRepository(Trick::class);
        $tricklistPaginated = $trickRepo->findTrickListPaginated($page, 10);

        $pictureRepo = $entityManager->getRepository(Picture::class);
        $pictureList = $pictureRepo->findAll();

        return $this->render('home/home.html.twig', [
            'trickList' => $tricklistPaginated,
            'pictureList' => $pictureList,
        ]);
    }

    #[Route('/trick-list', name: 'complete_trick_list')]
    public function tricktList(EntityManagerInterface $entityManager): Response
    {
        $repo = $entityManager->getRepository(Trick::class);
        $trickList = $repo->findAll();

        $pictureRepo = $entityManager->getRepository(Picture::class);
        $pictureList = $pictureRepo->findAll();

        return $this->render('home/home.html.twig', [
            'trickList' => $trickList,
            'pictureList' => $pictureList,
        ]);
    }
}
