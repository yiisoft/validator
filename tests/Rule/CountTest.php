<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\SerializableRuleInterface;

final class CountTest extends AbstractRuleTest
{
    public function testGetName(): void
    {
        $rule = new Count(min: 3);
        $this->assertSame('count', $rule->getName());
    }

    public function optionsDataProvider(): array
    {
        return [
            [
                new Count(min: 3),
                [
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'min' => 3,
                    'max' => null,
                    'exactly' => null,
                    'message' => [
                        'message' => 'This value must be an array or implement \Countable interface.',
                    ],
                    'lessThanMinMessage' => [
                        'message' => 'This value must contain at least {min, number} {min, plural, one{item} ' .
                            'other{items}}.',
                        'parameters' => ['min' => 3],
                    ],
                    'greaterThanMaxMessage' => [
                        'message' => 'This value must contain at most {max, number} {max, plural, one{item} ' .
                            'other{items}}.',
                        'parameters' => ['max' => null],
                    ],
                    'notExactlyMessage' => [
                        'message' => 'This value must contain exactly {exactly, number} {exactly, plural, one{item} ' .
                            'other{items}}.',
                        'parameters' => ['exactly' => null],
                    ],
                ],
            ],
        ];
    }

    public function initWithMinAndMaxAndExactlyDataProvider(): array
    {
        return [
            [['min' => 3, 'exactly' => 3]],
            [['max' => 3, 'exactly' => 3]],
            [['min' => 3, 'max' => 3, 'exactly' => 3]],
        ];
    }

    /**
     * @dataProvider initWithMinAndMaxAndExactlyDataProvider
     */
    public function testInitWithMinAndMaxAndExactly(array $arguments): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$exactly is mutually exclusive with $min and $max.');

        new Count(...$arguments);
    }

    public function testInitWithMinAndMax(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Use $exactly instead.');

        new Count(min: 3, max: 3);
    }

    public function testInitWithoutRequiredArguments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one of these attributes must be specified: $min, $max, $exactly.');

        new Count();
    }

    protected function getRule(): SerializableRuleInterface
    {
        return new Count(min: 1);
    }
}
