<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use InvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

/**
 * An implementation for {@see LimitInterface} intended to be included in rules. The following arguments need to be
 * added in constructor and passed with {@see initLimitProperties()} call:
 *
 * ```php
 * public function __construct(
 *     // ...
 *     float|int|null $min = null,
 *     float|int|null $max = null,
 *     float|int|null $exactly = null,
 *     string $lessThanMinMessage = 'Less than {min}.',
 *     string $greaterThanMinMessage = 'Greater than {max}.',
 *     string $greaterThanMinMessage = 'Not exactly {exactly}.',
 *     // ...
 * ) {
 *     // ...
 *     $this->initLimitProperties(
 *         $min,
 *         $max,
 *         $exactly,
 *         $lessThanMinMessage,
 *         $greaterThanMaxMessage,
 *         $notExactlyMessage,
 *     );
 *     // ...
 * }
 * ```
 *
 * Also, if a rule implements {@see RuleWithOptionsInterface}, you can merge limit related options instead of adding it
 * manually:
 *
 * ```php
 * public function getOptions(): array
 * {
 *     return array_merge($this->getLimitOptions(), [
 *         // Other rule options.
 *     ]);
 * }
 * ```
 *
 * Make sure to include {@see LimitHandlerTrait} in according handler as well.
 */
trait LimitTrait
{
    /**
     * @var float|int|null Minimum limit. Can't be combined with {@see $exactly}.
     *
     * @see $lessThanMinMessage for related error message.
     */
    private float|int|null $min = null;
    /**
     * @var float|int|null Maximum limit. Can't be combined with {@see $exactly}.
     *
     * @see $greaterThanMaxMessage for related error message.
     */
    private float|int|null $max = null;
    /**
     * @var float|int|null "Exactly" number. A shortcut / replacement for the case when {@see $min} and {@see $max} have the
     * same not-null value. Mutually exclusive with both {@see $min} and {@see $max}. `null` means no strict comparison
     * so lower / upper limits / both must be set.
     *
     * @see $notExactlyMessage for related error message.
     */
    private float|int|null $exactly = null;
    /**
     * @var string Validation error message used when a validated value is less than minimum set in {@see $min}.
     */
    private string $lessThanMinMessage;
    /**
     * @var string Validation error message used when a validated value is greater than maximum set in {@see $max}.
     */
    private string $greaterThanMaxMessage;
    /**
     * @var string Validation error message used when a validated value doesn't exactly match the one set in
     * {@see $exactly}.
     */
    private string $notExactlyMessage;

    /**
     * Initializes limit related properties and runs checks for required, mutually exclusive properties and their
     * allowed values (including dependency on each other).
     *
     * @param float|int|null $min Minimum limit ({@see $min}).
     * @param float|int|null $max Maximum limit ({@see $max}).
     * @param float|int|null $exactly "Exactly" number ({@see $exactly}).
     * @param string $lessThanMinMessage "Less than minimum" validation error message ({@see $lessThanMinMessage}).
     * @param string $greaterThanMinMessage "Greater than maximum" validation error message
     * ({@see $greaterThanMinMessage}).
     * @param string $notExactlyMessage "Not exactly" validation error message ({@see $notExactlyMessage}).
     */
    private function initLimitProperties(
        float|int|null $min,
        float|int|null $max,
        float|int|null $exactly,
        string $lessThanMinMessage,
        string $greaterThanMinMessage,
        string $notExactlyMessage,
        bool $requireLimits = true,
        bool $allowNegativeLimits = false,
    ): void {
        $this->min = $min;
        $this->max = $max;
        $this->exactly = $exactly;
        $this->lessThanMinMessage = $lessThanMinMessage;
        $this->greaterThanMaxMessage = $greaterThanMinMessage;
        $this->notExactlyMessage = $notExactlyMessage;

        if ($this->min === null && $this->max === null && $this->exactly === null) {
            if ($requireLimits === false) {
                return;
            }

            $message = 'At least one of these attributes must be specified: $min, $max, $exactly.';

            throw new InvalidArgumentException($message);
        }

        if (($this->min !== null || $this->max !== null) && $this->exactly !== null) {
            throw new InvalidArgumentException('$exactly is mutually exclusive with $min and $max.');
        }

        if (
            $allowNegativeLimits === false &&
            (
                ($this->min !== null && $this->min <= 0) ||
                ($this->max !== null && $this->max <= 0) ||
                ($this->exactly !== null && $this->exactly <= 0)
            )
        ) {
            throw new InvalidArgumentException('Only positive values are allowed.');
        }

        if ($this->min !== null && $this->max !== null) {
            if ($this->min > $this->max) {
                throw new InvalidArgumentException('$min must be lower than $max.');
            }

            if ($this->min === $this->max) {
                throw new InvalidArgumentException('Use $exactly instead.');
            }
        }
    }

    /**
     * A getter for {@see $min} property.
     *
     * @return float|int|null A number representing minimum boundary. `null` means no lower bound.
     */
    public function getMin(): float|int|null
    {
        return $this->min;
    }

    /**
     * A getter for {@see $max property}.
     *
     * @return float|int|null A number representing maximum boundary. `null` means no upper bound.
     */
    public function getMax(): float|int|null
    {
        return $this->max;
    }

    /**
     * A getter for {@see $exactly} property.
     *
     * @return float|int|null A number representing "exactly" value. `null` means no strict comparison so lower / upper limits /
     * both must be set.
     */
    public function getExactly(): float|int|null
    {
        return $this->exactly;
    }

    /**
     * A getter for {@see $lessThanMinMessage} property.
     *
     * @return string Validation error message.
     */
    public function getLessThanMinMessage(): string
    {
        return $this->lessThanMinMessage;
    }

    /**
     * A getter for {@see $greaterThanMaxMessage} property.
     *
     * @return string Validation error message.
     */
    public function getGreaterThanMaxMessage(): string
    {
        return $this->greaterThanMaxMessage;
    }

    /**
     * A getter for {@see $notExactlyMessage} property.
     *
     * @return string Validation error message.
     */
    public function getNotExactlyMessage(): string
    {
        return $this->notExactlyMessage;
    }

    /**
     * Limit related options intended to be merged with other rule options.
     *
     * @return array<string, mixed> A map between property name and property value.
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
                'template' => $this->lessThanMinMessage,
                'parameters' => ['min' => $this->min],
            ],
            'greaterThanMaxMessage' => [
                'template' => $this->greaterThanMaxMessage,
                'parameters' => ['max' => $this->max],
            ],
            'notExactlyMessage' => [
                'template' => $this->notExactlyMessage,
                'parameters' => ['exactly' => $this->exactly],
            ],
        ];
    }
}
