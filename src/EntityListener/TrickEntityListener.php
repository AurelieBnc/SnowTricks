<?php 

namespace App\EntityListener;

use App\Entity\Trick;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;

#[AsEntityListener(event: Events::prePersist, entity: Trick::class)]
#[AsEntityListener(event: Events::preUpdate, entity: Trick::class)]
class TrickEntityListener
{
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function prePersist(Trick $trick, LifecycleEventArgs $event): void
    {
        $trick->computeSlug($this->slugger);
    }

    public function preUpdate(Trick $trick, LifecycleEventArgs $event): void
    {
        $trick->computeSlug($this->slugger);
    }
}
