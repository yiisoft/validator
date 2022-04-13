<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Callback;

use Closure;

final class Callback
{
    public function __construct(
        /**
         * @var callable
         */
        public          $callback,
        public bool     $skipOnEmpty = false,
        public bool     $skipOnError = false,
        public ?Closure $when = null,
    ) {

    }
}
