<?php

namespace App\Service;

class LinkYoutubeService 
{

    /**
     * function to convert shareLink into displayLink of Youtube
     */
    public function intoEmbedLinkYoutube(string $shareLink): string
    {
        $displayLink = str_replace('youtu.be', 'youtube.com/embed', $shareLink);

        return $displayLink;
    }
}
