<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Error;

final class ErrorTest extends TestCase
{
    public function dataGetValuePath(): array
    {
        return [
            'null' => [
                ['user', 'data.age'],
                ['user', 'data.age'],
                null,
            ],
            'symbol' => [
                ['user', 'the.data\-age'],
                ['user', 'the.data-age'],
                '-',
            ],
            'emoji' => [
                ['user', 'the.data\😎age'],
                ['user', 'the.data😎age'],
                '😎',
            ],
            'dot' => [
                ['user', 'data\.age'],
                ['user', 'data.age'],
                '.',
            ],
        ];
    }

    /**
     * @dataProvider dataGetValuePath
     */
    public function testGetValuePath(array $expectedValuePath, array $valuePath, bool|string|null $escape): void
    {
        $error = new Error('', valuePath: $valuePath);

        $this->assertSame($expectedValuePath, $error->getValuePath($escape));
    }

    public function testTooLongEscapeSymbol(): void
    {
        $error = new Error('');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Escape symbol must be exactly one character.');
        $error->getValuePath('..');
    }
}
