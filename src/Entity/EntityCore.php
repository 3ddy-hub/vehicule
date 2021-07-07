<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class EntityCore
 * @package App\Entity
 */
class EntityCore
{
    /**
     * @ORM\Column(type="datetime")
     */
    protected $date_add;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $date_update;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $is_deleted = 0;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    protected $status;

    public function __construct()
    {
        $this->date_add = new \DateTime();
    }

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->date_add;
    }

    public function setDateAdd(\DateTimeInterface $date_add): self
    {
        $this->date_add = $date_add;

        return $this;
    }

    public function getDateUpdate(): ?\DateTimeInterface
    {
        return $this->date_update;
    }

    public function setDateUpdate(?\DateTimeInterface $date_update): self
    {
        $this->date_update = $date_update;

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->is_deleted;
    }

    public function setIsDeleted(bool $is_deleted): self
    {
        $this->is_deleted = $is_deleted;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
