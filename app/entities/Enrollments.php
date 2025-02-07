<?php

namespace App\entities;

use DateTime;

class Enrollments
{
    private int $id;
    private DateTime $enrolledAt;
    private ?DateTime $completedAt;
    private bool $isCompleted;

    public function __construct(int $id, DateTime $enrolledAt, ?DateTime $completedAt = null, bool $isCompleted = false)
    {
        $this->id = $id;
        $this->enrolledAt = $enrolledAt;
        $this->completedAt = $completedAt;
        $this->isCompleted = $isCompleted;
    }
    public function getId(): int
    {
        return $this->id;
    }

    public function getEnrolledAt(): DateTime
    {
        return $this->enrolledAt;
    }

    public function getCompletedAt(): ?DateTime
    {
        return $this->completedAt;
    }

    public function isCompleted(): bool
    {
        return $this->isCompleted;
    }
    public function setCompletedAt(?DateTime $completedAt): void
    {
        $this->completedAt = $completedAt;
    }

    public function setIsCompleted(bool $isCompleted): void
    {
        $this->isCompleted = $isCompleted;
    }
}