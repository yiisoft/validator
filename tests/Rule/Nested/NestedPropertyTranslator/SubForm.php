<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Nested\NestedPropertyTranslator;

use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorProviderInterface;
use Yiisoft\Validator\Rule\Length;

final class SubForm implements PropertyTranslatorProviderInterface
{
    #[Length(min: 5)]
    public string $phone = '123';

    public function getPropertyTranslator(): ?PropertyTranslatorInterface
    {
        return new ArrayPropertyTranslator([
            'phone' => 'Телефон',
        ]);
    }
}
