<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

trait LimitTrait
{
    private ?int $min = null;
    private ?int $max = null;
    private ?int $exactly = null;
    private string $lessThanMinMessage;
    private string $greaterThanMaxMessage;
    private string $notExactlyMessage;

    private function initLimitProperties(
        ?int $min,
        ?int $max,
        ?int $exactly,
        string $lessThanMinMessage,
        string $greaterThanMinMessage,
        string $notExactlyMessage
    ): void
    {
        $this->min = $min;
        $this->max = $max;
        $this->exactly = $exactly;
        $this->lessThanMinMessage = $lessThanMinMessage;
        $this->greaterThanMaxMessage = $greaterThanMinMessage;
        $this->notExactlyMessage = $notExactlyMessage;

        if (!$this->min && !$this->max && !$this->exactly) {
            throw new InvalidArgumentException(
                'At least one of these attributes must be specified: $min, $max, $exactly.'
            );
        }

        if ($this->exactly && ($this->min || $this->max)) {
            throw new InvalidArgumentException('$exactly is mutually exclusive with $min and $max.');
        }

        if ($this->min && $this->max && $this->min === $this->max) {
            throw new InvalidArgumentException('Use $exactly instead.');
        }
    }

    /**
     * @return int|null
     */
    public function getMin(): ?int
    {
        return $this->min;
    }

    /**
     * @return int|null
     */
    public function getMax(): ?int
    {
        return $this->max;
    }

    /**
     * @return int|null
     */
    public function getExactly(): ?int
    {
        return $this->exactly;
    }

    /**
     * @return string
     */
    public function getLessThanMinMessage(): string
    {
        return $this->lessThanMinMessage;
    }

    /**
     * @return string
     */
    public function getGreaterThanMaxMessage(): string
    {
        return $this->greaterThanMaxMessage;
    }

    /**
     * @return string
     */
    public function getNotExactlyMessage(): string
    {
        return $this->notExactlyMessage;
    }

    #[ArrayShape([
        'min' => 'int|null',
        'max' => 'int|null',
        'exactly' => 'int|null',
        'lessThanMinMessage' => 'array',
        'greaterThanMaxMessage' => 'array',
        'notExactlyMessage' => 'array',
    ])]
    private function getLimitOptions(): array
    {
        return [
            'min' => $this->min,
            'max' => $this->max,
            'exactly' => $this->exactly,
            'lessThanMinMessage' => [
                'message' => $this->lessThanMinMessage,
                'parameters' => ['min' => $this->min],
            ],
            'greaterThanMaxMessage' => [
                'message' => $this->greaterThanMaxMessage,
                'parameters' => ['max' => $this->max],
            ],
            'notExactlyMessage' => [
                'message' => $this->notExactlyMessage,
                'parameters' => ['exactly' => $this->exactly],
            ],
        ];
    }
}
