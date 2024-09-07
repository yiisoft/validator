<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use ArrayObject;
use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\GreaterThan;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class ObjectWithIterablePropertyRules implements RulesProviderInterface
{
    #[Required]
    public string $name = '';

    #[GreaterThan(5)]
    #[Number(min: 21)]
    protected int $age = 17;

    #[Number(max: 100)]
    private int $number = 42;

    public function getRules(): iterable
    {
        return [
            'age' => new ArrayObject([new Required(), new Equal(25)]),
        ];
    }
}
