<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use InvalidArgumentException;
use ReflectionObject;
use Yiisoft\Validator\AfterInitAttributeEventInterface;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Defines validation options to validating the value using a callback.
 *
 * @see CallbackHandler
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Callback implements
    RuleWithOptionsInterface,
    SkipOnErrorInterface,
    WhenInterface,
    SkipOnEmptyInterface,
    AfterInitAttributeEventInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    /**
     * @var object|null The object being validated.
     */
    private ?object $validatedObject = null;

    /**
     * @param callable|null $callback Callable with the `function ($value, $rule, $context): Result` signature that
     * performs the validation.
     * @param string|null $method Name of a validated object method with the `function ($value, $rule, $context): Result`
     * signature that performs the validation.
     * @param bool|callable|null $skipOnEmpty Whether to skip this rule if the value validated is empty.
     * See {@see SkipOnEmptyInterface}.
     * @param bool $skipOnError Whether to skip this rule if any of the previous rules gave an error.
     * See {@see SkipOnErrorInterface}.
     * @param Closure|null $when A callable to define a condition for applying the rule.
     * See {@see WhenInterface}.
     * @psalm-param WhenType $when
     * @throws InvalidArgumentException When either no callback or method is specified or
     * both are specified at the same time.
     */
    public function __construct(
        private mixed $callback = null,
        private string|null $method = null,
        private mixed $skipOnEmpty = null,
        private bool $skipOnError = false,
        private Closure|null $when = null,
    ) {
        if ($this->callback === null && $this->method === null) {
            throw new InvalidArgumentException('Either "$callback" or "$method" must be specified.');
        }

        if ($this->callback !== null && $this->method !== null) {
            throw new InvalidArgumentException('"$callback" and "$method" are mutually exclusive.');
        }
    }

    public function getName(): string
    {
        return 'callback';
    }

    /**
     * Get the callable that performs validation.
     *
     * @return callable|null The callable that performs validation.
     *
     * @see $callback
     */
    public function getCallback(): callable|null
    {
        return $this->callback;
    }

    /**
     * Get a name of a validated object method that performs the validation.
     *
     * @return string|null Name of a method that performs the validation.
     *
     * @see $method
     */
    public function getMethod(): string|null
    {
        return $this->method;
    }

    /**
     * Get object being validated.
     *
     * @return object|null Object being validated.
     *
     * @see $validatedObject
     */
    public function getValidatedObject(): ?object
    {
        return $this->validatedObject;
    }

    public function afterInitAttribute(object $object, int $target): void
    {
        if ($target === Attribute::TARGET_CLASS) {
            $this->validatedObject = $object;
        }

        if ($this->method === null) {
            return;
        }

        $method = $this->method;

        $reflection = new ReflectionObject($object);
        if (!$reflection->hasMethod($method)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Method "%s" does not exist in class "%s".',
                    $method,
                    $object::class,
                )
            );
        }

        /** @psalm-suppress MixedMethodCall */
        $this->callback = Closure::bind(fn (mixed ...$args): mixed => $object->{$method}(...$args), $object, $object);
    }

    public function getOptions(): array
    {
        return [
            'method' => $this->method,
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandler(): string
    {
        return CallbackHandler::class;
    }
}
