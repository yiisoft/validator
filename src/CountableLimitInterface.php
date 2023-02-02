<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

/**
 * An optional interface for rules to implement. Rules implementing it must have minimum and maximum limits and also
 * "exactly" value for strict equality. Boundaries must reflect numbers of countable items.
 *
 * The package ships with {@see CountableLimitTrait} which already implements that interface. All you have to do is to
 * include it in the rule class along with the interface.
 */
interface CountableLimitInterface
{
    /**
     * Returns current minimum limit.
     *
     * @return int|null A number representing minimum boundary. `null` means no lower bound.
     *
     * @see getLessThanMinMessage() for related error message.
     */
    public function getMin(): int|null;

    /**
     * Returns current maximum limit.
     *
     * @return int|null A number representing maximum boundary. `null` means no upper bound.
     *
     * @see getGreaterThanMaxMessage() for related error message.
     */
    public function getMax(): int|null;

    /**
     * Returns current "exactly" value meant for strict comparison. It is a shortcut for the case when {@see getMin()}
     * and {@see getMax()} have the same not null value.
     *
     * @return int|null A number representing "exactly" value. `null` means no strict comparison so lower / upper limits
     * / both must be set.
     *
     * @see getNotExactlyMessage() for related error message
     */
    public function getExactly(): int|null;

    /**
     * Returns message used when a validated value is less than minimum set in {@see getMin()}.
     *
     * @return string Validation error message.
     */
    public function getLessThanMinMessage(): string;

    /**
     * Returns message used when a validated value is greater than maximum in {@see getMax()}.
     *
     * @return string Validation error message.
     */
    public function getGreaterThanMaxMessage(): string;

    /**
     * Returns message used when a validated value doesn't exactly match the one set in {@see getExactly()}.
     *
     * @return string Validation error message.
     */
    public function getNotExactlyMessage(): string;
}
