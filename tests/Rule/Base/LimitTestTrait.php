<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Base;

use InvalidArgumentException;

trait LimitTestTrait
{
    abstract protected function getRuleClass(): string;

    public function dataInitWithMinAndMaxAndExactly(): array
    {
        return [
            [['min' => 3, 'exactly' => 3]],
            [['max' => 3, 'exactly' => 3]],
            [['min' => 3, 'max' => 3, 'exactly' => 3]],
        ];
    }

    /**
     * @dataProvider dataInitWithMinAndMaxAndExactly
     */
    public function testInitWithMinAndMaxAndExactly(array $arguments): void
    {
        $ruleClass = $this->getRuleClass();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$exactly is mutually exclusive with $min and $max.');
        new $ruleClass(...$arguments);
    }

    public function testInitWithMinAndMax(): void
    {
        $ruleClass = $this->getRuleClass();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Use $exactly instead.');
        new $ruleClass(min: 3, max: 3);
    }

    public function testInitWithoutRequiredArguments(): void
    {
        $ruleClass = $this->getRuleClass();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one of these attributes must be specified: $min, $max, $exactly.');
        new $ruleClass();
    }

    public function dataInitWithNonPositiveValues(): array
    {
        return [
            [['min' => 0, 'max' => 2]],
            [['min' => -1, 'max' => 2]],
            [['min' => 2, 'max' => 0]],
            [['min' => 2, 'max' => -1]],
            [['min' => -1, 'max' => 0]],
            [['min' => 0, 'max' => -1]],
            [['min' => -2, 'max' => -1]],
            [['exactly' => 0]],
            [['exactly' => -1]],
        ];
    }

    /**
     * @dataProvider dataInitWithNonPositiveValues
     */
    public function testInitWithNonPositiveValues(array $arguments): void
    {
        $ruleClass = $this->getRuleClass();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Only positive values are allowed.');
        new $ruleClass(...$arguments);
    }

    public function testInitWithMinGreaterThanMax(): void
    {
        $ruleClass = $this->getRuleClass();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$min must be lower than $max.');
        new $ruleClass(min: 2, max: 1);
    }
}
