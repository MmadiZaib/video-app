<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index as Index;

/**
 * @ORM\Entity(repositoryClass=VideoRepository::class)
 * @ORM\Table(name="videos", indexes={@Index(name="title_idx", columns={"title"})})
 */
class Video
{
    public const VIMEO_PATH = 'https://player.vimeo.com/video/';
    public const VIDEO_FOR_NOT_LOGGED_IN_OR_NO_MEMBERS = 113716040;
    public const PER_PAGE = 5;
    public const uploadFolder = '/uploads/videos/';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="videos")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="video", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="likedVideos")
     * @ORM\JoinTable(name="likes")
     */
    private $userThatLike;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="dislikedVideos")
     * @ORM\JoinTable(name="dislikes")
     */
    private $userThatDontLike;

    private $uploadVideo;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->userThatLike = new ArrayCollection();
        $this->userThatDontLike = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setVideo($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getVideo() === $this) {
                $comment->setVideo(null);
            }
        }

        return $this;
    }

    public function getVimeoId(): ?string
    {
        return self::VIMEO_PATH;
    }

    /**
     * @return Collection|User[]
     */
    public function getUserThatLike(): Collection
    {
        return $this->userThatLike;
    }

    public function addUserThatLike(User $userThatLike): self
    {
        if (!$this->userThatLike->contains($userThatLike)) {
            $this->userThatLike[] = $userThatLike;
        }

        return $this;
    }

    public function removeUserThatLike(User $userThatLike): self
    {
        $this->userThatLike->removeElement($userThatLike);

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUserThatDontLike(): Collection
    {
        return $this->userThatDontLike;
    }

    public function addUserThatDontLike(User $userThatDontLike): self
    {
        if (!$this->userThatDontLike->contains($userThatDontLike)) {
            $this->userThatDontLike[] = $userThatDontLike;
        }

        return $this;
    }

    public function removeUserThatDontLike(User $userThatDontLike): self
    {
        $this->userThatDontLike->removeElement($userThatDontLike);

        return $this;
    }

    public function getUploadVideo()
    {
        return $this->uploadVideo;
    }

    public function setUploadVideo($uploadVideo): self
    {
        $this->uploadVideo = $uploadVideo;

        return $this;
    }
}
