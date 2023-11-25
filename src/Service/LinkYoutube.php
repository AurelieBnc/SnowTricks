<?php

namespace App\Service;

class LinkYoutube 
{

    /**
     * function to convert shareLink into displayLink of Youtube
     */
    public function intoEmbedLinkYoutbe ($shareLink): string
    {
        $displayLink = str_replace('youtu.be', 'youtube.com/embed', $shareLink);

        return $displayLink;
    }
}
