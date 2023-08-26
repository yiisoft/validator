<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\AttributeTranslator\ArrayAttributeTranslator;
use Yiisoft\Validator\AttributeTranslatorInterface;
use Yiisoft\Validator\AttributeTranslatorProviderInterface;
use Yiisoft\Validator\Rule\OneOf;

#[OneOf(['a', 'b', 'c'])]
final class OneOfDto implements AttributeTranslatorProviderInterface
{
    public function __construct(
        public ?int $a = null,
        public ?int $b = null,
        public ?int $c = null,
    ) {
    }

    public function getAttributeLabels(): array
    {
        return [
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
        ];
    }

    public function getAttributeTranslator(): ?AttributeTranslatorInterface
    {
        return new ArrayAttributeTranslator($this->getAttributeLabels());
    }
}
