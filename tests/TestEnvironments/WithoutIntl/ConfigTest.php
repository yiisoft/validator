<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\TestEnvironments\WithoutIntl;

use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\MessageFormatterInterface;
use Yiisoft\Validator\Tests\BaseConfigTest;

final class ConfigTest extends BaseConfigTest
{
    public function testSimpleMessageFormatter(): void
    {
        $container = $this->createContainer();

        $customFormatter = new class () implements MessageFormatterInterface {
            public function format(string $message, array $parameters, string $locale): string
            {
                return 'test';
            }
        };

        /** @var CategorySource $translationCategorySource */
        $translationCategorySource = $container->get('tag@translation.categorySource')[0];
        $message = '{n, selectordinal, one{#-one} two{#-two} few{#-few} other{#-other}}';

        // The default formatter argument is ignored in favor of formatter set in config.
        $this->assertSame(
            '1',
            $translationCategorySource->format($message, ['n' => 1], 'en', $customFormatter),
        );
    }
}
