<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\EmailHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;


use function extension_loaded;

final class EmailTest extends TestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testGetName(): void
    {
        $rule = new Email();
        $this->assertSame('email', $rule->getName());
    }

    public function testEnableIdnWithMissingIntlExtension(): void
    {
        if (extension_loaded('intl')) {
            $this->markTestSkipped('The intl extension must be unavailable for this test.');
        }

        $this->expectException(RuntimeException::class);
        new Email(enableIDN: true);
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Email(), new Email(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Email(), new Email(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Email::class, EmailHandler::class];
    }
}
