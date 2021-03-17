<?php

namespace App\Classes;

use App\Entity\Campus;
use App\Entity\Status;
use App\Entity\User;
use Symfony\Component\Serializer\Annotation\Groups;

class Filters
{
    /**
     * @Groups("filters")
     */
    private ?string $campusName;

    /**
     * @Groups("filters")
     */
    private ?string $event;

    /**
     * @Groups("filters")
     */
    private ?string $dateFrom;

    /**
     * @Groups("filters")
     */
    private ?string $dateTo;

    /**
     * @Groups("filters")
     */
    private ?bool $userName;

    /**
     * @Groups("filters")
     */
    private ?bool $userSub;

    /**
     * @Groups("filters")
     */
    private ?bool $userNonsub;

    /**
     * @Groups("filters")
     */
    private ?bool $past;

    /**
     * @var Campus
     */
    private $campus;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Status
     */
    private $status;

    /**
     * @var array
     */
    private $eventNames;

    public function getCampusName(): ?string
    {
        return $this->campusName;
    }

    public function setCampusName(string $campusName): self
    {
        $this->campusName = $campusName;

        return $this;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function setEvent(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getDateFrom(): ?string
    {
        return $this->dateFrom;
    }

    public function setDateFrom(string $dateFrom): self
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function getDateTo(): ?string
    {
        return $this->dateTo;
    }

    public function setDateTo(string $dateTo): self
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    public function getUserName(): ?bool
    {
        return $this->userName;
    }

    public function setUserName(bool $userName): self
    {
        $this->userName = $userName;

        return $this;
    }

    public function getUserSub(): ?bool
    {
        return $this->userSub;
    }

    public function setUserSub(bool $userSub): self
    {
        $this->userSub = $userSub;

        return $this;
    }

    public function getUserNonsub(): ?bool
    {
        return $this->userNonsub;
    }

    public function setUserNonsub(bool $userNonsub): self
    {
        $this->userNonsub = $userNonsub;

        return $this;
    }

    public function getPast(): ?bool
    {
        return $this->past;
    }

    public function setPast(bool $past): self
    {
        $this->past = $past;

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getEventNames(): ?array
    {
        return $this->eventNames;
    }

    public function setEventNames(?array $eventNames): self
    {
        $this->eventNames = $eventNames;

        return $this;
    }
}