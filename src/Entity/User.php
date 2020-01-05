<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(indexes={
 *     @ORM\Index(name="discord_idx", columns={"discord_id"}),
 *     @ORM\Index(name="username_idx", columns={"username"})
 * })
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     */
    private ?UuidInterface $id = null;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private string $username = '';

    /**
     * @ORM\Column(type="json_array")
     */
    private array $roles = ['ROLE_USER'];

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private ?string $nickname = null;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private ?string $discordId = null;

    /**
     * @ORM\Column(type="string", length=4, options={"fixed":true}, nullable=true)
     */
    private ?string $discordTag = null;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     * @Gedmo\Timestampable(on="create")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetimetz_immutable")
     * @Gedmo\Timestampable(on="update")
     */
    private \DateTimeInterface $updatedAt;

    public function __construct(?UuidInterface $id = null)
    {
        $this->id = $id;
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        if (empty($this->roles)) {
            $this->roles[] = 'ROLE_USER';
        }

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getDiscordId(): ?string
    {
        return $this->discordId;
    }

    public function setDiscordId(?string $discordId): self
    {
        $this->discordId = $discordId;

        return $this;
    }

    public function getDiscordTag(): ?string
    {
        return $this->discordTag;
    }

    public function setDiscordTag(?string $discordTag): self
    {
        $this->discordTag = $discordTag;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
