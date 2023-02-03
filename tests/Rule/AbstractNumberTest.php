<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\AbstractNumber;
use Yiisoft\Validator\Rule\NumberHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;

final class AbstractNumberTest extends TestCase
{
    use DifferentRuleInHandlerTestTrait;

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [AbstractNumber::class, NumberHandler::class];
    }
}
