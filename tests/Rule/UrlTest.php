<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Validator\Rule\Url;
use Yiisoft\Validator\Rule\UrlHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\WhenTestTrait;

use function extension_loaded;

final class UrlTest extends TestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SkipOnErrorTestTrait;
    use WhenTestTrait;

    public function testDefaultValues(): void
    {
        $rule = new Url();

        $this->assertSame('url', $rule->getName());
        $this->assertSame(['http', 'https'], $rule->getValidSchemes());
    }

    public function testGetValidSchemes(): void
    {
        $rule = new Url(validSchemes: ['http', 'https', 'ftp', 'ftps']);
        $this->assertSame(['http', 'https', 'ftp', 'ftps'], $rule->getValidSchemes());
    }

    public function testEnableIdnWithMissingIntlExtension(): void
    {
        if (extension_loaded('intl')) {
            $this->markTestSkipped('The intl extension must be unavailable for this test.');
        }

        $this->expectException(RuntimeException::class);
        new Url(enableIDN: true);
    }

    public function testSkipOnError(): void
    {
        $this->testSkipOnErrorInternal(new Url(), new Url(skipOnError: true));
    }

    public function testWhen(): void
    {
        $when = static fn (mixed $value): bool => $value !== null;
        $this->testWhenInternal(new Url(), new Url(when: $when));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Url::class, UrlHandler::class];
    }
}
