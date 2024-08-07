<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Type;

use Attribute;
use Closure;
use Yiisoft\Validator\DumpedRuleInterface;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Defines validation options to check that the value has float type. Only native PHP float type -
 * {@link https://www.php.net/manual/en/language.types.float.php} is allowed. For example, the float number within a
 * string is discarded. If you need to support that, consider to use {@see Number} instead.
 *
 * @see FloatTypeHandler Corresponding handler performing the actual validation.
 *
 * @psalm-import-type SkipOnEmptyValue from SkipOnEmptyInterface
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class FloatType implements DumpedRuleInterface, SkipOnEmptyInterface, SkipOnErrorInterface, WhenInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @param string $message Error message used when validation fails because the type of the validated value is not
     * float.
     *
     * You may use the following placeholders in the message:
     *
     * - `{property}`: the translated label of the property being validated.
     * - `{type}`: the type of the value being validated.
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the validated value is empty / not passed.
     * See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error. See
     * {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule. See {@see WhenInterface}.
     *
     * @psalm-param SkipOnEmptyValue $skipOnEmpty
     * @psalm-param WhenType $when
     */
    public function __construct(
        private string $message = '{Property} must be a float.',
        bool|callable|null $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
        $this->skipOnEmpty = $skipOnEmpty;
    }

    public function getName(): string
    {
        return self::class;
    }

    /**
     * Gets error message used when validation fails because the type of validated value is not float.
     *
     * @return string Error message / template.
     *
     * @see $message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    public function getOptions(): array
    {
        return [
            'message' => [
                'template' => $this->message,
                'parameters' => [],
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandler(): string
    {
        return FloatTypeHandler::class;
    }
}
