<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: Types::INTEGER, unique: true, nullable: false)]
    protected int|null $id;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 200, unique: true, nullable: false)]
    private ?string $name;

    #[ORM\Column(name: 'email', type: Types::STRING, length: 200, unique: true, nullable: false)]
    private ?string $email;

    #[ORM\Column(name: 'phoneNumber', type: Types::STRING, length: 15, unique: true, nullable: false)]
    private ?string $phoneNumber;

    /**
     * @var Collection<int, Note>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Note::class)]
    private Collection $notes;

    public function __construct(
        ?string $name,
        ?string $email,
        ?string $phoneNumber,
        ?int $id = null,
    ) {
        $this->id    = $id;
        $this->name  = $name;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->notes = new ArrayCollection();
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function __toArray(): array
    {
        return [
            'id'         => $this->getId(),
            'name'       => $this->getName(),
            'email'      => $this->getEmail(),
        ];
    }
}
