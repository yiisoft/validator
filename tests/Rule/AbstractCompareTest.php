<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\AbstractCompare;
use Yiisoft\Validator\Rule\CompareHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;

final class AbstractCompareTest extends TestCase
{
    use DifferentRuleInHandlerTestTrait;

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [AbstractCompare::class, CompareHandler::class];
    }
}
