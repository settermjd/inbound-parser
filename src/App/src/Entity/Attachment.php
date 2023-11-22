<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AttachmentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttachmentRepository::class)]
#[ORM\Table(name: 'attachment')]
class Attachment
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: Types::INTEGER, unique: true, nullable: false)]
    protected int|null $id;

    #[ORM\Column(name: 'file', type: Types::BLOB, unique: false, nullable: true)]
    private $file;

    #[ORM\ManyToOne(targetEntity: Note::class, inversedBy: 'attachments')]
    private ?Note $note;

    public function __construct(
        ?string $file,
        ?Note   $note,
        ?int    $id
    ) {
        $this->id   = $id;
        $this->note = $note;
        $this->file = $file;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getNote(): ?Note
    {
        return $this->note;
    }

    public function __toArray(): array
    {
        return [
            'id'        => $this->getId(),
            'file'      => $this->getFile(),
        ];
    }
}
