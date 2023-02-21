<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\TestEnvironments\Php81\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Tests\TestEnvironments\Support\Data\CompositeWithCallbackAttribute;
use Yiisoft\Validator\Validator;

final class CompositeTest extends TestCase
{
    public function testWithCallbackAttribute(): void
    {
        $result = (new Validator())->validate(new CompositeWithCallbackAttribute());

        $this->assertSame(
            [
                '' => ['Invalid A.'],
                'b' => ['Invalid B.'],
            ],
            $result->getErrorMessagesIndexedByAttribute()
        );
    }
}
