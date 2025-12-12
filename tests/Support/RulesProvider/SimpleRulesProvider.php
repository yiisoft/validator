<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\RulesProvider;

use Yiisoft\Validator\RulesProviderInterface;

final class SimpleRulesProvider implements RulesProviderInterface
{
    public function __construct(
        private readonly iterable $rules,
    ) {}

    public function getRules(): iterable
    {
        return $this->rules;
    }
}
