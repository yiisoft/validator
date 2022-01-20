<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Formatter;

final class FormatterTest extends TestCase
{
    public function testImmutability(): void
    {
        $formatter = new Formatter();
        $this->assertNotSame($formatter, $formatter->locale('en_US'));
    }

    public function testLocale(): void
    {
        $formatter = new Formatter();
        $formatter = $formatter->locale('ru_UA');
        $message = '{0,number,integer} мавп на {1,number,integer} деревах це {2,number} мавпи на кожному деревi';
        $parameters = [0 => 4560, 1 => 123, 2 => 4560/123];
        $this->assertSame('4 560 мавп на 123 деревах це 37,073 мавпи на кожному деревi', $formatter->format($message, $parameters));
    }
}
