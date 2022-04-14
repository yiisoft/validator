<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Callback;

use Closure;
use Yiisoft\Validator\Rule\RuleNameTrait;
use Yiisoft\Validator\Rule\ValidatorClassNameTrait;
use Yiisoft\Validator\RuleInterface;

final class Callback implements RuleInterface
{
    use RuleNameTrait;
    use ValidatorClassNameTrait;

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
