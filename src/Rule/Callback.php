<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use InvalidArgumentException;
use TypeError;
use Yiisoft\Validator\AttributeEventInterface;
use Yiisoft\Validator\BeforeValidationInterface;
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Rule\Trait\BeforeValidationTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\ValidationContext;

use function get_class;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Callback implements
    SerializableRuleInterface,
    BeforeValidationInterface,
    SkipOnEmptyInterface,
    AttributeEventInterface
{
    use BeforeValidationTrait;
    use RuleNameTrait;
    use SkipOnEmptyTrait;

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

    /**
     * @return callable|null
     */
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

        try {
            $this->callback = Closure::fromCallable([get_class($dataSet->getObject()), $this->method]);
        } catch (TypeError) {
            throw new InvalidArgumentException('Method must exist and have public and static modifers.');
        }
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
