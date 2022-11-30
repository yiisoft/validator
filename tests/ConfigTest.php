<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Validator\RuleHandlerResolverInterface;
use Yiisoft\Validator\SimpleRuleHandlerContainer;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;

use function dirname;
use function extension_loaded;

final class ConfigTest extends TestCase
{
    public function testBase(): void
    {
        $container = $this->createContainer();

        $validator = $container->get(ValidatorInterface::class);
        $this->assertInstanceOf(Validator::class, $validator);

        $ruleHandlerResolver = $container->get(RuleHandlerResolverInterface::class);
        $this->assertInstanceOf(SimpleRuleHandlerContainer::class, $ruleHandlerResolver);

        /** @var CategorySource[] $translationCategorySources */
        $translationCategorySources = $container->get('tag@translation.categorySource');
        $this->assertCount(1, $translationCategorySources);

        $translationCategorySource = $translationCategorySources[0];
        $this->assertInstanceOf(CategorySource::class, $translationCategorySource);
        $this->assertSame(Validator::DEFAULT_TRANSLATION_CATEGORY, $translationCategorySource->getName());
    }

    public function testCustomTranslationCategory(): void
    {
        $params = [
            'yiisoft/validator' => [
                'translation.category' => 'yii-validator-custom',
            ],
        ];
        $container = $this->createContainer($params);

        /** @var CategorySource $translationCategorySource */
        $translationCategorySource = $container->get('tag@translation.categorySource')[0];
        $this->assertSame('yii-validator-custom', $translationCategorySource->getName());
    }

    public function testIntlMessageFormatter(): void
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('The intl extension must be available for this test.');
        }

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

    public function testSimpleMessageFormatter(): void
    {
        if (extension_loaded('intl')) {
            $this->markTestSkipped('The intl extension must be unavailable for this test.');
        }

        $container = $this->createContainer();

        /** @var CategorySource $translationCategorySource */
        $translationCategorySource = $container->get('tag@translation.categorySource')[0];
        $message = '{n, selectordinal, one{#-one} two{#-two} few{#-few} other{#-other}}';
        // The default formatter argument is ignored in favor of formatter set in config.
        $this->assertSame(
            '1',
            $translationCategorySource->format($message, ['n' => 1], 'en', new SimpleMessageFormatter()),
        );
    }

    public function testTranslationCategorySource(): void
    {
        $container = $this->createContainer();

        /** @var CategorySource[] $translationCategorySource */
        $translationCategorySources = $container->get('tag@translation.categorySource');
        $this->assertCount(1, $translationCategorySources);

        $translationCategorySource = $translationCategorySources[0];
        $this->assertInstanceOf(CategorySource::class, $translationCategorySource);

        $this->assertSame('Значение неверно.', $translationCategorySource->getMessage('This value is invalid.', 'ru'));
    }

    private function createContainer(array|null $params = null): Container
    {
        $config = ContainerConfig::create()->withDefinitions($this->getCommonDefinitions($params));

        return new Container($config);
    }

    private function getCommonDefinitions(array|null $params): array
    {
        if ($params === null) {
            $params = $this->getParams();
        }

        return require dirname(__DIR__) . '/config/common.php';
    }

    private function getParams(): array
    {
        return require dirname(__DIR__) . '/config/params.php';
    }
}
