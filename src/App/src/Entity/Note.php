<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\NoteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NoteRepository::class)]
#[ORM\Table(name: 'note')]
#[ORM\Index(columns: ['details'], name: 'note_idx')]
class Note
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: Types::INTEGER, unique: true, nullable: false)]
    protected int|null $id;

    #[ORM\Column(name: 'details', type: Types::TEXT, unique: false, nullable: true)]
    private ?string $description;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'notes')]
    private ?User $user;

    /**
     * @var Collection<int, Attachment>
     */
    #[ORM\OneToMany(mappedBy: 'note', targetEntity: Attachment::class)]
    private Collection $attachments;

    public function __construct(
        ?int    $id,
        ?string $details,
        ?User   $user,
    ) {
        $this->id   = $id;
        $this->description = $details;
        $this->user = $user;
        $this->attachments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function addAttachment(Attachment $attachment): void
    {
        $this->attachments->add($attachment);
    }

    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function __toArray(): array
    {
        return [
            'id'         => $this->getId(),
            'details'    => $this->getDescription(),
        ];
    }
}
