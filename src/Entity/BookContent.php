<?php

namespace App\Entity;

use App\Repository\SubscriberRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: SubscriberRepository::class)]
class BookContent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type:"text")]
    private string $content;

    #[ORM\Column(type:"boolean", options: ['default' => false])]
    private bool $isPublished = false;

    #[ORM\JoinColumn(nullable: false)]
    #[ORM\ManyToOne(targetEntity: BookChapter::class)]
    private BookChapter $chapter;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getChapter(): BookChapter
    {
        return $this->chapter;
    }

    public function setChapter(BookChapter $chapter): self
    {
        $this->chapter = $chapter;

        return $this;
    }
}
