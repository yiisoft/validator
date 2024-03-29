<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\TestEnvironments\WithoutIntl\Rule\Date;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Validator\Rule\Date\Date;
use Yiisoft\Validator\Validator;

final class DateTest extends TestCase
{
    public function testEnableIdnWithMissingIntlExtension(): void
    {
        $rule = new Date(format: 'YYYY-MM-dd');
        $validator = new Validator();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The "intl" PHP extension is required to parse date.');
        $validator->validate('', $rule);
    }
}
