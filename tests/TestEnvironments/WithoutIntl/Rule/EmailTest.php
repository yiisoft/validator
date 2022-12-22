<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\TestEnvironments\WithoutIntl\Rule;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Validator\Rule\Email;

final class EmailTest extends TestCase
{
    public function testEnableIdnWithMissingIntlExtension(): void
    {
        $this->expectException(RuntimeException::class);
        new Email(enableIdn: true);
    }
}
