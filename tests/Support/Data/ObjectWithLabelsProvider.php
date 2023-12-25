<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Label;
use Yiisoft\Validator\LabelsProviderInterface;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

final class ObjectWithLabelsProvider implements LabelsProviderInterface
{
    #[Required(message: '{attribute} cannot be blank.')]
    public string $name = '';

    #[Number(min: 21, lessThanMinMessage: '{attribute} must be no less than {min}.')]
    #[Label('test age')]
    protected int $age = 17;

    #[Number(max: 100)]
    #[Label('test')]
    private int $number = 42;

    public function getValidationPropertyLabels(): array
    {
        return [
            'name' => 'Имя',
            'age' => 'Возраст',
        ];
    }
}
