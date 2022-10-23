<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

/**
 * A trait attachable to a rule with limits.
 *
 * @see LimitHandlerTrait
 */
trait LimitTrait
{
    /**
     * @var int|null Lower limit.
     *
     * @see $lessThanMinMessage for according error message. Can't be combined with {@see $exactly}.
     */
    private ?int $min = null;
    /**
     * @var int|null Upper limit.
     *
     * @see $greaterThanMaxMessage for according error message. Can't be combined with {@see $exactly}.
     */
    private ?int $max = null;
    /**
     * @var int|null Exact number (lower and upper limit match).
     *
     * @see $notExactlyMessage for according error message. `null` means no strict comparison. Mutually exclusive with
     * {@see $min} and {@see $max}.
     */
    private ?int $exactly = null;
    /**
     * @var string Message used when validated value is lower than {@see $min}.
     */
    private string $lessThanMinMessage;
    /**
     * @var string Message used when validated value is greater than {@see $max}.
     */
    private string $greaterThanMaxMessage;
    /**
     * @var string Message used when validated value doesn't exactly match {@see $exactly}.
     */
    private string $notExactlyMessage;

    /**
     * Initializes limit related properties and runs checks for required and mutually exclusive properties.
     *
     * @param int|null $min {@see $min}
     * @param int|null $max {@see $max}
     * @param int|null $exactly {@see $exactly}
     * @param string $lessThanMinMessage {@see $lessThanMinMessage}
     * @param string $greaterThanMinMessage {@see $greaterThanMinMessage}
     * @param string $notExactlyMessage {@see $notExactlyMessage}
     */
    private function initLimitProperties(
        ?int $min,
        ?int $max,
        ?int $exactly,
        string $lessThanMinMessage,
        string $greaterThanMinMessage,
        string $notExactlyMessage
    ): void {
        $this->min = $min;
        $this->max = $max;
        $this->exactly = $exactly;
        $this->lessThanMinMessage = $lessThanMinMessage;
        $this->greaterThanMaxMessage = $greaterThanMinMessage;
        $this->notExactlyMessage = $notExactlyMessage;

        if ($this->min === null && $this->max === null && $this->exactly === null) {
            throw new InvalidArgumentException(
                'At least one of these attributes must be specified: $min, $max, $exactly.'
            );
        }

        if ($this->exactly !== null && ($this->min !== null || $this->max !== null)) {
            throw new InvalidArgumentException('$exactly is mutually exclusive with $min and $max.');
        }

        if ($this->min === $this->max && $this->min !== null) {
            throw new InvalidArgumentException('Use $exactly instead.');
        }
    }

    /**
     * Gets lower limit.
     *
     * @return int|null {@see $min}
     */
    public function getMin(): ?int
    {
        return $this->min;
    }

    /**
     * Gets upper limit.
     *
     * @return int|null {@see $max}
     */
    public function getMax(): ?int
    {
        return $this->max;
    }

    /**
     * Gets "exactly" value.
     *
     * @return int|null {@see $exactly}
     */
    public function getExactly(): ?int
    {
        return $this->exactly;
    }

    /**
     * Gets "less than lower limit" error message.
     *
     * @return string {@see $lessThanMinMessage}
     */
    public function getLessThanMinMessage(): string
    {
        return $this->lessThanMinMessage;
    }

    /**
     * Gets "greater than upper limit" error message.
     *
     * @return string {@see $greaterThanMaxMessage}
     */
    public function getGreaterThanMaxMessage(): string
    {
        return $this->greaterThanMaxMessage;
    }

    /**
     * Gets "does not match exactly" error message.
     *
     * @return string {@see notExactlyMessage}
     */
    public function getNotExactlyMessage(): string
    {
        return $this->notExactlyMessage;
    }

    /**
     * Limit related options intended to be merged with other rule options.
     */
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
