<?php

namespace App\Service\Recommendation\Modal;

class RecomendationResponse
{
    public function __construct(
        private int $id,
        private int $ts,
        private array $recommendation
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTs(): int
    {
        return $this->ts;
    }

    public function setTs(int $ts): self
    {
        $this->ts = $ts;

        return $this;
    }

    /**
     * @return RecommendationItem[]
     */
    public function getRecommendation(): array
    {
        return $this->recommendation;
    }

    public function setRecommendation(array $recommendation): self
    {
        $this->recommendation = $recommendation;

        return $this;
    }
}