<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Picture;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function home(EntityManagerInterface $entityManager, ?UserInterface $user): Response
    {
        if ($user) {
            if ($user->isVerified() === false) {
                $this->addFlash('verification', 'Vous devez confirmer votre adresse email.');
                $this->redirectToRoute('app_home');
            }
            if (!$user->isVerified() === false) {
                $this->addFlash('success', 'Une bonne journée à toi '.$user->getUsername().' coeur coeur !');
                $this->redirectToRoute('app_home');
            }
        }
        /** @var App\Repository\TrickRepository $repo */
        $repo = $entityManager->getRepository(Trick::class);
        $trickList = $repo->trickListMaxFifteen();
        $countTrickList = $repo->countTrickList();
        $loader = true;

        $pictureRepo = $entityManager->getRepository(Picture::class);
        $pictureList = $pictureRepo->findAll();

        return $this->render('home/home.html.twig', [
            'trickList' => $trickList,
            'countTrickList' => $countTrickList,
            'loader' => $loader,
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
