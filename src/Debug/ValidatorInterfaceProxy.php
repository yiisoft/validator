<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Debug;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;

final class ValidatorInterfaceProxy implements ValidatorInterface
{
    public function __construct(
        private readonly ValidatorInterface $validator,
        private readonly ValidatorCollector $collector,
    ) {
    }

    public function validate(
        mixed $data,
        callable|iterable|object|string|null $rules = null,
        ?ValidationContext $context = null
    ): Result {
        $result = $this->validator->validate($data, $rules, $context);

        if ($rules === null && $data instanceof RulesProviderInterface) {
            $rules = (array) $data->getRules();
        }

        $this->collector->collect(
            $data,
            $result,
            $rules,
        );

        return $result;
    }
}
