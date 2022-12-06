<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\LimitInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Trait\LimitHandlerTrait;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Support\Rule\RuleWithoutOptions;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;
use Yiisoft\Validator\ValidationContext;

final class LimitHandlerTraitTest extends TestCase
{
    public function validateLimitsWithWrongRuleData(): array
    {
        return [
            [
                new class () implements LimitInterface {
                    public function getMin(): int|null
                    {
                        return null;
                    }

                    public function getMax(): int|null
                    {
                        return null;
                    }

                    public function getExactly(): int|null
                    {
                        return 1;
                    }

                    public function getLessThanMinMessage(): string
                    {
                        return 'less then min message';
                    }

                    public function getGreaterThanMaxMessage(): string
                    {
                        return 'greater then min mesage';
                    }

                    public function getNotExactlyMessage(): string
                    {
                        return 'not exactly message';
                    }
                },
            ],
            [
                new RuleWithoutOptions(),
            ],
        ];
    }

    /**
     * @dataProvider validateLimitsWithWrongRuleData
     */
    public function testValidateLimitsWithWrongRule(LimitInterface|RuleInterface $rule): void
    {
        $handler = new class () implements RuleHandlerInterface {
            use LimitHandlerTrait;

            public function validate(mixed $value, object $rule, ValidationContext $context): Result
            {
                return new Result();
            }

            public function checkValidateLimits(
                LimitInterface|RuleInterface $rule,
                ValidationContext $context,
                int $number,
                Result $result
            ): void {
                $this->validateLimits($rule, $context, $number, $result);
            }
        };
        $context = new ValidationContext(ValidatorFactory::make(), dataSet: null);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$rule must implement both LimitInterface and RuleInterface.');
        $handler->checkValidateLimits($rule, $context, 1, new Result());
    }
}
