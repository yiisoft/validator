<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub\EachNestedObjects;

use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class Foo implements RulesProviderInterface
{
    public ?string $name = null;

    public array $bars;

    public function __construct()
    {
        $this->bars = [
            new Bar(),
        ];
    }

    public function getRules(): iterable
    {
        yield from [
            'name' => new Required(),
            'bars' => new Each([new Nested(Bar::class)]),
        ];
    }
}
