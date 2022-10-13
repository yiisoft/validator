<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Compare;
use Yiisoft\Validator\Rule\CompareHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;

final class CompareTest extends TestCase
{
    use DifferentRuleInHandlerTestTrait;

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Compare::class, CompareHandler::class];
    }
}
