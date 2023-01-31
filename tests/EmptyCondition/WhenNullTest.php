<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\EmptyCondition;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\EmptyCondition\WhenNull;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Validator;

final class WhenNullTest extends TestCase
{
    public function dataBase(): array
    {
        return [
            [
                [],
                null,
                new Number(skipOnEmpty: new WhenNull()),
            ],
            [
                [],
                [],
                ['property' => new Number(skipOnEmpty: new WhenNull())],
            ],
            [
                ['Value must be a number.'],
                '',
                new Number(skipOnEmpty: new WhenNull()),
            ],
        ];
    }

    /**
     * @dataProvider dataBase
     */
    public function testBase(array $expectedMessages, mixed $data, array|RuleInterface|null $rules = null): void
    {
        $result = (new Validator())->validate($data, $rules);

        $this->assertSame($expectedMessages, $result->getErrorMessages());
    }
}
