<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\TestEnvironments\WithoutIntl\Rule;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Validator\Rule\Url;

final class UrlTest extends TestCase
{
    public function testEnableIdnWithMissingIntlExtension(): void
    {
        $this->expectException(RuntimeException::class);
        new Url(enableIdn: true);
    }
}
