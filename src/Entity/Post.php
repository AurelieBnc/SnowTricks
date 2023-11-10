<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updateDate = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Media::class, cascade:['persist'])]
    private Collection $mediaList;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $commentList;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Picture::class, cascade:['persist'])]
    private Collection $pictureList;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $headerImage = null;

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

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updateDate;
    }

    public function setUpdateDate(?\DateTimeInterface $updateDate): static
    {
        $this->updateDate = $updateDate;

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
            $media->setPost($this);
        }

        return $this;
    }

    public function removeMedia(Media $media): static
    {
        if ($this->mediaList->removeElement($media)) {
            // set the owning side to null (unless already changed)
            if ($media->getPost() === $this) {
                $media->setPost(null);
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
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->commentList->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
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

    public function addPicture(Picture $pictureList): static
    {
        if (!$this->pictureList->contains($pictureList)) {
            $this->pictureList->add($pictureList);
            $pictureList->setPost($this);
        }

        return $this;
    }

    public function removePicture(Picture $pictureList): static
    {
        if ($this->pictureList->removeElement($pictureList)) {
            // set the owning side to null (unless already changed)
            if ($pictureList->getPost() === $this) {
                $pictureList->setPost(null);
            }
        }

        return $this;
    }

    public function getHeaderImage(): ?string
    {
        return $this->headerImage;
    }

    public function setHeaderImage(?string $headerImage): static
    {
        $this->headerImage = $headerImage;

        return $this;
    }
}
