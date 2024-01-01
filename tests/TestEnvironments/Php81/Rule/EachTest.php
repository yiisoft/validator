<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\TestEnvironments\Php81\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Tests\TestEnvironments\Support\Data\EachDto;
use Yiisoft\Validator\Validator;

final class EachTest extends TestCase
{
    public function testClassAttribute(): void
    {
        $result = (new Validator())->validate(new EachDto(1, 0, 3));

        $this->assertSame(
            [
                'a' => ['The value must be zero.'],
                'c' => ['The value must be zero.'],
            ],
            $result->getErrorMessagesIndexedByAttribute()
        );
    }
}
