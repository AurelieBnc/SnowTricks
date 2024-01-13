<?php

namespace App\Entity;

use App\Repository\TrickRepository;
use DateTimeImmutable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\SluggerInterface;

#[ORM\Entity(repositoryClass: TrickRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['title'], message: 'Un trick avec le même titre existe déjà.')]
class Trick
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updateDate = null;

    #[ORM\ManyToOne(inversedBy: 'tricks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Media::class, cascade:['persist','remove'])]
    private Collection $mediaList;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Comment::class, orphanRemoval: true, cascade:['remove'])]
    private Collection $commentList;

    #[ORM\OneToMany(mappedBy: 'trick', targetEntity: Picture::class, cascade:['persist','remove'])]
    private Collection $pictureList;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $headerPictureName = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    public function __construct()
    {
        $this->mediaList = new ArrayCollection();
        $this->commentList = new ArrayCollection();
        $this->pictureList = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(): static
    {
        $this->createdAt = new DateTimeImmutable();

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updateDate;
    }

    #[ORM\PreUpdate]
    public function setUpdateDate(): static
    {
        $this->updateDate = new DateTimeImmutable();
        
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMediaList(): Collection
    {
        return $this->mediaList;
    }

    public function addMedia(Media $media): static
    {
        if (!$this->mediaList->contains($media)) {
            $this->mediaList->add($media);
            $media->setTrick($this);
        }

        return $this;
    }

    public function removeMedia(Media $media): static
    {
        if ($this->mediaList->removeElement($media)) {
            // set the owning side to null (unless already changed)
            if ($media->getTrick() === $this) {
                $media->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getCommentList(): Collection
    {
        return $this->commentList;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->commentList->contains($comment)) {
            $this->commentList->add($comment);
            $comment->setTrick($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->commentList->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getTrick() === $this) {
                $comment->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictureList(): Collection
    {
        return $this->pictureList;
    }

    public function addPicture(Picture $picture): static
    {
        if (!$this->pictureList->contains($picture)) {
            $this->pictureList->add($picture);
            $picture->setTrick($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): static
    {
        if ($this->pictureList->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getTrick() === $this) {
                $picture->setTrick(null);
            }
        }

        return $this;
    }

    public function getHeaderPictureName(): ?string
    {
        return $this->headerPictureName;
    }

    public function setHeaderPictureName(?string $headerPictureName): static
    {
        $this->headerPictureName = $headerPictureName;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function computeSlug(SluggerInterface $slugger): static
    {
        $this->slug = strtolower($slugger->slug($this->title));

        return $this;
    }
}
