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
                $this->addFlash('verification', 'Tu dois confirmer ton adresse email.');
            } else {
                $this->addFlash('hello', 'Une bonne journée à toi '.$user->getUsername().', Ride On !');
            }
            $this->redirectToRoute('app_home');
        }
        $page = $request->query->getInt('page', 1);

        /** @var App\Repository\TrickRepository $repo */
        $trickRepo = $entityManager->getRepository(Trick::class);
        $tricklistPaginated = $trickRepo->findTrickListPaginated($page, 10);

        return $this->render('home/home.html.twig', [
            'trickList' => $tricklistPaginated,
        ]);
    }
}
