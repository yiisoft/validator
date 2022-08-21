<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\Rule\Equal;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\RulesProviderInterface;

final class ObjectWithRulesProvider implements RulesProviderInterface
{
    #[Required]
    public string $name = '';

    #[Number(min: 21)]
    protected int $age = 17;

    #[Number(max: 100)]
    private int $number = 42;

    public function getRules(): iterable
    {
        return [
            'age' => [new Required(), new Equal(25)],
        ];
    }
}
