<?php

namespace App\Service;

class SortContext
{
    private function __construct(private SorPosition $position, private int $nearId)
    {
    }

    public static function fromNeighbours(?int $nextId, ?int $previusId): self
    {
        $position = match (true) {
            null === $previusId && null !== $nextId => SorPosition::AsFirst,
            null !== $previusId && null === $nextId => SorPosition::AsLast,
            default => SorPosition::Between
        };


        return new self($position, SorPosition::AsLast === $position ? $previusId : $nextId);
    }

    public function getPosition(): SorPosition
    {
        return $this->position;
    }

    public function getNearId(): int
    {
        return $this->nearId;
    }
}