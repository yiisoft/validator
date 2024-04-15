<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Closure;
use Attribute;
use InvalidArgumentException;
use Yiisoft\Validator\WhenInterface;
use Yiisoft\Validator\DumpedRuleInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;

/**
 * Defines validation options to check that the value is a date.
 *
 * An example for simple  that can be used to validate the date:
 * ```php
 * use Yiisoft\Validator\Rule\DateTime;
 *
 * $rules = [
 *      'date' => [
 *          new DateTime(format: 'Y-m-d'),
 *      ],
 * ];
 * ```
 * In the example above, the PHP attributes equivalent will be:
 *
 * ```php
 * use Yiisoft\Validator\Validator;
 * use Yiisoft\Validator\Rule\DateTime;
 *
 * final class User
 * {
 *     public function __construct(
 *          #[DateTime(format: 'Y-m-d')]
 *          public string $date,
 *     ){
 *    }
 * }
 *
 * $user = new User( date: '2022-01-01' );
 *
 * $validator = (new Validator())->validate($user);
 *
 * ```
 *
 * @see DateTimeHandler
 *
 * @psalm-import-type SkipOnEmptyValue from SkipOnEmptyInterface
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class DateTime implements DumpedRuleInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @link https://www.php.net/manual/en/datetimeimmutable.createfromformat.php
     * @psalm-var non-empty-string
     * @var string The allowed date formats.
     */
    private string $format;

    /**
     * @param string $format The format of the date. See {@see $format}
     * @param string $message A message used when the value is not valid.
     * You may use the following placeholders in the message:
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{value}`: the value of the attribute being validated.
     * @param string $incorrectInputMessage A message used when the input is incorrect.
     * You may use the following placeholders in the message:
     * - `{attribute}`: the translated label of the attribute being validated.
     * - `{type}`: the type of the value being validated.
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the value validated is empty. See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error. See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule. See {@see WhenInterface}.
     *
     * @psalm-param SkipOnEmptyValue $skipOnEmpty
     * @psalm-param WhenType $when
     */
    public function __construct(
        string $format = 'Y-m-d',
        private string $incorrectInputMessage = '{Attribute} must be a date.',
        private string $message = '{Attribute} is not a valid date.',
        bool|callable|null $skipOnEmpty = null,
        private bool $skipOnError = false,
        private ?Closure $when = null,
    ) {
        if ($format === '') {
            throw new InvalidArgumentException('Format can\'t be empty.');
        }

        $this->format = $format;
        $this->skipOnEmpty = $skipOnEmpty;
    }

    /**
     *  The date format.
     *
     * @return string The format. See {@see $format}
     * @psalm-return non-empty-string
     *
     * @see $format
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Get a message used when the input is incorrect.
     *
     * @return string A message used when the input is incorrect.
     * @see $incorrectInputMessage
     */
    public function getIncorrectInputMessage(): string
    {
        return $this->incorrectInputMessage;
    }

    public function getOptions(): array
    {
        return [
            'format' => $this->format,
            'incorrectInputMessage' => [
                'template' => $this->incorrectInputMessage,
                'parameters' => [],
            ],
            'message' => [
                'template' => $this->message,
                'parameters' => [],
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getName(): string
    {
        return self::class;
    }

    public function getHandler(): string|RuleHandlerInterface
    {
        return DateTimeHandler::class;
    }
}
