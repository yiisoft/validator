<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\AttributeTranslator\ArrayAttributeTranslator;
use Yiisoft\Validator\AttributeTranslatorInterface;
use Yiisoft\Validator\AttributeTranslatorProviderInterface;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\RulesProviderInterface;

final class SimpleForm implements RulesProviderInterface, AttributeTranslatorProviderInterface
{
    public function __construct(
        public string $name = '',
        public string $mail = '',
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function getAttributeLabels(): array
    {
        return [
            'name' => 'Имя',
            'mail' => 'Почта',
        ];
    }

    public function getAttributeTranslator(): ?AttributeTranslatorInterface
    {
        return new ArrayAttributeTranslator($this->getAttributeLabels());
    }

    public function getRules(): iterable
    {
        return [
            'name' => [
                new Length(min: 8, lessThanMinMessage: '{attribute} плохое.'),
            ],
            'mail' => [
                new Email(),
            ],
        ];
    }
}
