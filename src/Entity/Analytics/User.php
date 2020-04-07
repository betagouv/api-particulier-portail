<?php

namespace App\Entity\Analytics;


class User
{
    /**
     * @var string
     */
    private $email;

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}
