<?php

namespace App\Manager;

use App\Entity\Media;
use App\Entity\Trick;
use App\Service\LinkYoutubeService;
use Doctrine\ORM\EntityManagerInterface;

class MediaManager
{
    private $entityManager;
    private $linkYoutubeService;

    public function __construct(
        EntityManagerInterface $entityManager,
        LinkYoutubeService $linkYoutubeService,
    ) {
        $this->entityManager = $entityManager;
        $this->linkYoutubeService = $linkYoutubeService;
    }

    public function add(Trick $trick,string $url): void
    {
        $media = new Media;
        $urlModified = $this->linkYoutubeService->intoEmbedLinkYoutube($url);

        $media->setVideoUrl($urlModified);
        $trick->addMedia($media);

        $this->entityManager->persist($trick);
    }

    public function edit(Media $media): void
    {
        $urlModified = $this->linkYoutubeService->intoEmbedLinkYoutube($media->getVideoUrl());
        $media->setVideoUrl($urlModified); 

        $this->entityManager->persist($media);
    }

}
