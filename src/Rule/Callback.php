<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use InvalidArgumentException;
use ReflectionException;
use ReflectionObject;
use Yiisoft\Validator\AttributeEventInterface;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Callback implements
    SerializableRuleInterface,
    SkipOnErrorInterface,
    WhenInterface,
    SkipOnEmptyInterface,
    AttributeEventInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    public function __construct(
        /**
         * @var callable|null
         */
        private $callback = null,
        private ?string $method = null,

        /**
         * @var bool|callable|null
         */
        private $skipOnEmpty = null,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
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

    public function getCallback(): ?callable
    {
        return $this->callback;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function afterInitAttribute(DataSetInterface $dataSet): void
    {
        if (!$dataSet instanceof ObjectDataSet) {
            return;
        }

        $object = $dataSet->getObject();
        $method = $this->method;

        $reflection = new ReflectionObject($object);
        if (!$reflection->hasMethod($method)) {
            throw new InvalidArgumentException(sprintf(
                'Method "%s" does not exist in class "%s".',
                $method,
                $object::class,
            ));
        }

        $this->callback = Closure::bind(fn (...$args) => $object->{$method}(...$args), $object, $object);
    }

    public function getOptions(): array
    {
        return [
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandlerClassName(): string
    {
        return CallbackHandler::class;
    }
}
