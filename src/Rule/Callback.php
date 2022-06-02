<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Closure;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\PreValidatableRuleInterface;
use Yiisoft\Validator\Rule\Trait\PreValidatableTrait;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\Rule\Trait\HandlerClassNameTrait;
use Yiisoft\Validator\ParametrizedRuleInterface;

final class Callback implements ParametrizedRuleInterface, PreValidatableRuleInterface
{
    use HandlerClassNameTrait;
    use PreValidatableTrait;
    use RuleNameTrait;

    public function __construct(
        /**
         * @var callable
         */
        private $callback,
        private bool $skipOnEmpty = false,
        private bool $skipOnError = false,
        private ?Closure $when = null,
    ) {
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
}
