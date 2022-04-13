<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Callback;

use Closure;
use Yiisoft\Validator\ParametrizedRuleInterface;
use Yiisoft\Validator\Rule\RuleNameTrait;

final class Callback implements ParametrizedRuleInterface
{
    use RuleNameTrait;

    public function __construct(
        /**
         * @var callable
         */
        public          $callback,
        public bool $skipOnEmpty = false,
        public bool $skipOnError = false,
        public ?Closure $when = null,
    ) {
    }

    public function getOptions(): array
    {
        return [
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
