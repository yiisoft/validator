<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Base;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;

trait CountableLimitTestTrait
{
    public static function dataInitWithMinAndMaxAndExactly(): array
    {
        return [
            [['min' => 3, 'exactly' => 3]],
            [['max' => 3, 'exactly' => 3]],
            [['min' => 3, 'max' => 3, 'exactly' => 3]],
            [['min' => 0, 'max' => 0, 'exactly' => 0]],
        ];
    }

    #[DataProvider('dataInitWithMinAndMaxAndExactly')]
    public function testInitWithMinAndMaxAndExactly(array $arguments): void
    {
        $ruleClass = $this->getRuleClass();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$exactly is mutually exclusive with $min and $max.');
        new $ruleClass(...$arguments);
    }

    public static function dataUseExactlyInstead(): array
    {
        return [
            [['min' => 3, 'max' => 3]],
            [['min' => 0, 'max' => 0]],
        ];
    }

    #[DataProvider('dataUseExactlyInstead')]
    public function testUseExactlyInstead(array $arguments): void
    {
        $ruleClass = $this->getRuleClass();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Use $exactly instead.');
        new $ruleClass(...$arguments);
    }

    public function testInitWithoutRequiredArguments(): void
    {
        $ruleClass = $this->getRuleClass();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one of these properties must be specified: $min, $max, $exactly.');
        new $ruleClass();
    }

    public static function dataInitWithNonPositiveValues(): array
    {
        return [
            [['min' => -1, 'max' => 2]],
            [['min' => 2, 'max' => -1]],
            [['min' => -1, 'max' => 0]],
            [['min' => 0, 'max' => -1]],
            [['min' => -2, 'max' => -1]],
            [['exactly' => -1]],
            [['min' => -1]],
            [['max' => -1]],
        ];
    }

    #[DataProvider('dataInitWithNonPositiveValues')]
    public function testInitWithNonPositiveValues(array $arguments): void
    {
        $ruleClass = $this->getRuleClass();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Only positive or zero values are allowed.');
        new $ruleClass(...$arguments);
    }

    public function testInitWithMinGreaterThanMax(): void
    {
        $ruleClass = $this->getRuleClass();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$min must be lower than $max.');
        new $ruleClass(min: 2, max: 1);
    }

    /**
     * @return class-string
     */
    abstract protected function getRuleClass(): string;
}
