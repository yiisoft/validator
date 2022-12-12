<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\TestEnvironments\Php81\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Tests\Support\Data\StopOnErrorDto;
use Yiisoft\Validator\Validator;

final class StopOnErrorTest extends TestCase
{
    public function testClassAttribute(): void
    {
        $result = (new Validator())->validate(new StopOnErrorDto());

        $this->assertSame(
            [
                '' => ['error A'],
            ],
            $result->getErrorMessagesIndexedByAttribute()
        );
    }
}
