<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data\NestedHookProvider;

use Yiisoft\Validator\PostValidationHookInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;

class NestedObjectWithPostValidationHook implements PostValidationHookInterface
{
    #[Required]
    #[StringValue]
    #[Length(min: 6)]
    public string $firstString;

    #[Each(
        [
            new StringValue(),
            new Length(min: 6),
        ]
    )]
    public array $firstArray;

    #[Nested(SecondNestedObjectWithPostValidationHook::class)]
    public SecondNestedObjectWithPostValidationHook $secondNested;

    public ?Result $validationResult = null;

    public function __construct()
    {
        $this->secondNested = new SecondNestedObjectWithPostValidationHook();
    }

    public function processValidationResult(Result $result): void
    {
        $this->validationResult = $result;
    }
}
