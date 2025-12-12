<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorProviderInterface;
use Yiisoft\Validator\Rule\FilledAtLeast;

#[FilledAtLeast(['a', 'b', 'c'])]
final class AtLeastDto implements PropertyTranslatorProviderInterface
{
    public function __construct(
        public ?int $a = null,
        public ?int $b = null,
        public ?int $c = null,
    ) {}

    public function getPropertyLabels(): array
    {
        return [
            'a' => 'A',
            'b' => 'B',
            'c' => 'C',
        ];
    }

    public function getPropertyTranslator(): ?PropertyTranslatorInterface
    {
        return new ArrayPropertyTranslator($this->getPropertyLabels());
    }
}
