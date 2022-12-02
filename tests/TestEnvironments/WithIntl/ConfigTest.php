<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\TestEnvironments\WithIntl;

use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Validator\Tests\BaseConfigTest;

final class ConfigTest extends BaseConfigTest
{
    public function testIntlMessageFormatter(): void
    {
        $container = $this->createContainer();

        /** @var CategorySource $translationCategorySource */
        $translationCategorySource = $container->get('tag@translation.categorySource')[0];
        $message = '{n, selectordinal, one{#-one} two{#-two} few{#-few} other{#-other}}';
        // The default formatter argument is ignored in favor of formatter set in config.
        $this->assertSame(
            '1-one',
            $translationCategorySource->format($message, ['n' => 1], 'en', new SimpleMessageFormatter()),
        );
    }
}
