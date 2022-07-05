<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Closure;
use Yiisoft\Validator\Exception\InvalidCallbackReturnTypeException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\SelfValidatableRuleInterface;
use Yiisoft\Validator\Rule\Trait\BeforeValidationTrait;
use Yiisoft\Validator\Rule\Trait\HandlerClassNameTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\ValidationContext;

class CallbackSelfValidatableRule implements SelfValidatableRuleInterface
{
    use BeforeValidationTrait;
    use HandlerClassNameTrait;
    use RuleNameTrait;

    private Formatter $formatter;

    public function __construct(
        /**
         * @var callable
         */
        private $callback,
        private bool $skipOnEmpty = false,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    ) {
        $this->formatter = new Formatter();
    }

    /**
     * @return callable
     */
    public function getCallback(): callable
    {
        return $this->callback;
    }

    #[ArrayShape(['skipOnEmpty' => 'bool', 'skipOnError' => 'bool'])]
    public function getOptions(): array
    {
        return [
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function validate(mixed $value, ?ValidationContext $context = null): Result
    {
        $callback = $this->getCallback();
        $callbackResult = $callback($value, $context);

        if (!$callbackResult instanceof Result) {
            throw new InvalidCallbackReturnTypeException($callbackResult);
        }

        $result = new Result();
        if ($callbackResult->isValid()) {
            return $result;
        }

        foreach ($callbackResult->getErrors() as $error) {
            $formattedMessage = $this->formatter->format(
                $error->getMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage, $error->getValuePath());
        }

        return $result;
    }
}
