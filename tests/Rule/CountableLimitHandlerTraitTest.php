<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\CountableLimitInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Trait\CountableLimitHandlerTrait;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\Tests\Support\Rule\RuleWithoutOptions;
use Yiisoft\Validator\ValidationContext;

final class CountableLimitHandlerTraitTest extends TestCase
{
    public static function validateLimitsWithWrongRuleData(): array
    {
        return [
            [
                new class implements CountableLimitInterface {
                    public function getMin(): ?int
                    {
                        return null;
                    }

                    public function getMax(): ?int
                    {
                        return null;
                    }

                    public function getExactly(): ?int
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

    #[DataProvider('validateLimitsWithWrongRuleData')]
    public function testValidateLimitsWithWrongRule(CountableLimitInterface|RuleInterface $rule): void
    {
        $handler = new class implements RuleHandlerInterface {
            use CountableLimitHandlerTrait;

            public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result
            {
                return new Result();
            }

            public function checkValidateLimits(
                CountableLimitInterface|RuleInterface $rule,
                ValidationContext $context,
                int $number,
                Result $result,
            ): void {
                $this->validateCountableLimits($rule, $context, $number, $result);
            }
        };
        $context = new ValidationContext();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$rule must implement both LimitInterface and RuleInterface.');
        $handler->checkValidateLimits($rule, $context, 1, new Result());
    }
}
