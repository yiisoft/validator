<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorProviderInterface;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\RulesProviderInterface;

final class SimpleForm implements RulesProviderInterface, PropertyTranslatorProviderInterface
{
    public function __construct(
        public string $name = '',
        public string $mail = '',
    ) {
    }

    /**
     * @psalm-return array<string, string>
     */
    public function getAttributeLabels(): array
    {
        return [
            'name' => 'Имя',
            'mail' => 'Почта',
        ];
    }

    public function getPropertyTranslator(): ?PropertyTranslatorInterface
    {
        return new ArrayPropertyTranslator($this->getAttributeLabels());
    }

    public function getRules(): array
    {
        return [
            'name' => [
                new Length(min: 8, lessThanMinMessage: '{property} плохое.'),
            ],
            'mail' => [
                new Email(),
            ],
        ];
    }
}
