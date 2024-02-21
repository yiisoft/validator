<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;

use function is_bool;

/**
 * An implementation for {@see SkipOnEmptyInterface} intended to be included in rules. Requires an additional private
 * class property `$skipOnEmpty`. In package rules it's `null` by default:
 *
 * ```php
 * public function __construct(
 *     // ...
 *     private mixed $skipOnEmpty = null,
 *     // ...
 * ) {
 * }
 * ```
 *
 * @psalm-import-type SkipOnEmptyValue from SkipOnEmptyInterface
 */
trait SkipOnEmptyTrait
{
    /**
     * An immutable setter to change `$skipOnEmpty` property.
     *
     * @param bool|callable|null $value A new value.
     *
     * @return $this The new instance with a changed value.
     *
     * @psalm-param SkipOnEmptyValue $value
     */
    public function skipOnEmpty(bool|callable|null $value): static
    {
        $new = clone $this;
        $new->skipOnEmpty = $value;
        return $new;
    }

    /**
     * A getter for `$skipOnEmpty` property.
     *
     * @return bool|callable|null A current raw (non-normalized) value.
     *
     * @psalm-return SkipOnEmptyValue
     */
    public function getSkipOnEmpty(): bool|callable|null
    {
        return $this->skipOnEmpty;
    }

    /**
     * A special method used to cast `$skipOnEmpty` property for serialization to be possible. Used when building
     * {@see RuleWithOptionsInterface::getOptions()}. The missing details need to be recreated separately on the client
     * side.
     *
     * @return bool|null A casted value:
     *
     * - `true` - skip an empty value.
     * - `false` - do not skip an empty value.
     * - `null` - unable to determine because the callable was initially passed.
     */
    private function getSkipOnEmptyOption(): bool|null
    {
        if (is_bool($this->skipOnEmpty)) {
            return $this->skipOnEmpty;
        }

        if ($this->skipOnEmpty === null) {
            return false;
        }

        return null;
    }
}
