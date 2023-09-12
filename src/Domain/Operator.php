<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Base\Common;
use App\Domain\Base\Timestamps;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use TXC\Box\Interfaces\DomainInterface;

#[ORM\Entity(repositoryClass: \App\Repository\OperatorRepository::class)]
class Operator extends Common implements DomainInterface
{
    #[
        ORM\Id,
        ORM\Column(options: ['unsigned' => true]),
        ORM\GeneratedValue(strategy: 'AUTO'),
    ]
    protected ?int $id = null;

    #[ORM\Column(type: 'text', unique: true)]
    protected string $username;

    #[ORM\Column(type: 'text')]
    protected string $password;

    /**
     * @return Collection<int, Token>
     */
    #[ORM\OneToMany(targetEntity: Token::class, mappedBy: 'operator')]
    protected Collection $tokens;

    /**
     * @return Collection<int, Check>
     */
    #[ORM\OneToMany(targetEntity: Check::class, mappedBy: 'operator')]
    protected Collection $checks;

    public function __construct()
    {
        $this->tokens = new ArrayCollection();
        $this->checks = new ArrayCollection();
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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_DEFAULT);

        return $this;
    }

    /**
     * @return Collection<int, Token>
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    public function addToken(Token $token): self
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens->add($token);
        }

        return $this;
    }

    public function removeToken(Token $token): self
    {
        if ($this->tokens->contains($token)) {
            $this->tokens->removeElement($token);
        }

        return $this;
    }

    /**
     * @return Collection<int, Check>
     */
    public function getChecks(): Collection
    {
        return $this->checks;
    }

    public function addCheck(Check $check): self
    {
        if (!$this->checks->contains($check)) {
            $this->checks->add($check);
        }

        return $this;
    }

    public function removeCheck(Check $check): self
    {
        if ($this->checks->contains($check)) {
            $this->checks->removeElement($check);
        }

        return $this;
    }
}
