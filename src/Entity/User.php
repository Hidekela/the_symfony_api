<?php
/**
 * @author <julienrajerison5@gmail.com>
 *
 * This file is part of techzara_platform | all right reserve to the_challengers https://github.com/7he-Challenger
 */
declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 *
 * @ApiResource(
 *     security="is_granted('ROLE_ADMIN') or is_granted('ROLE_USER')",
 *     normalizationContext={"groups"="read"},
 *     denormalizationContext={"groups"="write"},
 *     collectionOperations={
 *          "get",
 *          "post" = {
 *              "validation_groups"={"Default", "create"}
 *          }
 *     }
 *)
 * @ApiFilter(DateFilter::class, properties={"createdAt"})
 * @ApiFilter(SearchFilter::class, properties={"username":"partial", "firstname":"partial", "lastname": "partial", "isEnable": "exact"})
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column()
     * @ORM\GeneratedValue()
     * @Groups({"read"})
     */
    private int $id;

    /**
     * @ORM\Column(type="text")
     */
    private string $password;

    /**
     * @ORM\Column(type="string", unique=true)
     *
     * @Groups({"read", "write"})
     * @Assert\NotBlank(groups={"create"})
     */
    private string $username;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Groups({"read", "write"})
     */
    private ?string $firstname = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Groups({"read", "write"})
     */
    private ?string $lastname = null;

    /**
     * @ORM\Column(type="simple_array")
     *
     * @Groups({"read", "write"})
     */
    private array $roles;

    /**
     * @ORM\OneToOne(targetEntity=UserInformation::class, cascade={"persist", "remove"})
     *
     * @Groups({"read", "write"})
     */
    private ?UserInformation $userInfo;

    /**
     * @var bool|null
     *
     * @ORM\Column(type="boolean")
     */
    private bool $isEnable;

    /**
     * @var string|null
     *
     * @Groups("write")
     *
     * @SerializedName("password")
     * @Assert\NotBlank(groups={"create"})
     */
    private ?string $plainPassword = '';

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read"})
     */
    private DateTime $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Presence::class, mappedBy="user")
     */
    private Collection $presences;

    public function __construct()
    {
        $this->isEnable = true;
        $this->presences = new ArrayCollection();
        $this->createdAt = new DateTime('now');
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): User
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     * @param array|null $role
     *
     * @return User
     */
    public function setRoles(?array $role): User
    {
        $this->roles = is_array($role) ? $role : ['ROLE_USER'];

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * @param string|null $firstname
     *
     * @return User
     */
    public function setFirstname(?string $firstname): User
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * @param string|null $lastname
     *
     * @return User
     */
    public function setLastname(?string $lastname): User
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials(): string
    {
        return $this->plainPassword = '';
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return UserInformation|null
     */
    public function getUserInfo(): ?UserInformation
    {
        return $this->userInfo;
    }

    /**
     * @param UserInformation|null $userInfo
     *
     * @return User
     */
    public function setUserInfo(?UserInformation $userInfo): User
    {
        $this->userInfo = $userInfo;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsEnable(): ?bool
    {
        return $this->isEnable;
    }

    /**
     * @param bool|null $isEnable
     *
     * @return User
     */
    public function setIsEnable(?bool $isEnable): User
    {
        $this->isEnable = $isEnable;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getPresences(): ArrayCollection
    {
        return $this->presences;
    }

    /**
     * @param Presence $presence
     *
     * @return User
     */
    public function addPresence(Presence $presence): User
    {
        if (!$this->presences->contains($presence)) {
            $this->presences->add($presence);
        }

        return $this;
    }

    /**
     * @param Presence $presence
     *
     * @return User
     */
    public function removePresence(Presence $presence): User
    {
        $this->presences->removeElement($presence);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     *
     * @return User
     */
    public function setPlainPassword(?string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime|null $createdAt
     *
     * @return User
     */
    public function setCreatedAt(?DateTime $createdAt): User
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function __call($name, $arguments)
    {
        return $this->username;
    }
}
