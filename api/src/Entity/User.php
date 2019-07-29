<?php

namespace App\Entity;

//use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ApiResource(
 *     attributes={"access_control"="is_granted('ROLE_ADMIN')"},
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User implements UserInterface
{
    /**
     * @var Uuid
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true, nullable=false)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true, nullable=false)
     * @Assert\NotNull
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     * @Assert\NotNull
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=false)
     */
    private $enabled;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="json_array", nullable=false)
     */
    private $roles;

    /**
     * @var string
     *
     * @ORM\Column(name="spotify_credentials", type="json_array")
     */
    private $spotify_credentials;

    /**
     * @var string
     *
     * @ORM\Column(name="spotify_list_owner", type="boolean", nullable=false, options={"default" : 0})
     */
    private $spotify_list_owner;

    /**
     * User constructor.
     * @param string $username
     * @param string $password
     * @param array $roles
     * @param bool $enabled
     * @throws \Exception
     */
    public function __construct(string $username, string $password, array $roles = [], bool $enabled = true, bool $spotify_list_owner = false)
    {

        if (strlen($username) < 5) {
            throw new \InvalidArgumentException('Username too short, length must be >= 5 !');
        }

        $this->id = Uuid::uuid4();
        $this->username = $username;
        $this->password = $password;
        $this->enabled = $enabled;
        $this->roles = $roles;
        $this->spotify_credentials = [
            'access_token' => '',
            'refresh_token' => ''
        ];
        $this->spotify_list_owner = $spotify_list_owner;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getSalt(): string
    {
        return '';
    }

    public function eraseCredentials()
    {
        return null;
    }

    public function getAccessToken(): string
    {
        return $this->spotify_credentials['access_token'];
    }

    public function setAccessToken(string $accessToken): void
    {
        $this->spotify_credentials['access_token'] = $accessToken;
    }

    public function setRefreshToken(string $refreshToken): void
    {
        $this->spotify_credentials['refresh_token'] = $refreshToken;
    }

    public function getRefreshToken(): string
    {
        return $this->spotify_credentials['refresh_token'];
    }

    public function setSpotifyListOwner(bool $spotify_list_owner): void
    {
        $this->spotify_list_owner = $spotify_list_owner;
    }

    public function getSpotifyListOwner(): bool
    {
        return $this->spotify_list_owner;
    }
}
