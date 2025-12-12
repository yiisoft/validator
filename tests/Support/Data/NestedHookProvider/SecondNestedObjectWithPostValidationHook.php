<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data\NestedHookProvider;

use Yiisoft\Validator\PostValidationHookInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StringValue;

class SecondNestedObjectWithPostValidationHook implements PostValidationHookInterface
{
    #[Required]
    #[Integer(min: 5, max: 10)]
    public int $secondInt = 15;
    #[StringValue]
    #[Length(min: 10)]
    public string $secondString = 'short';
    public ?Result $validationResult = null;

    public function __construct() {}

    public function processValidationResult(Result $result): void
    {
        $this->validationResult = $result;
    }
}
