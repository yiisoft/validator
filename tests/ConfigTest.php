<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;

use function dirname;

final class ConfigTest extends TestCase
{
    public function testBase(): void
    {
        $container = $this->createContainer();

        $validator = $container->get(ValidatorInterface::class);
        $this->assertInstanceOf(Validator::class, $validator);

        $translationCategorySource = $this->getTranslationCategorySource($container);
        $this->assertSame('yii-validator', $translationCategorySource->getName());
    }

    public function testCustomTranslationCategory(): void
    {
        $params = [
            'yiisoft/validator' => [
                'translation.category' => 'yii-validator-custom',
            ],
        ];
        $container = $this->createContainer($params);

        $translationCategorySource = $this->getTranslationCategorySource($container);
        $this->assertSame('yii-validator-custom', $translationCategorySource->getName());
    }

    private function getTranslationCategorySource(Container $container): CategorySource
    {
        /** @var CategorySource[] $translationCategorySource */
        $translationCategorySources = $container->get('tag@translation.categorySource');
        $this->assertCount(1, $translationCategorySources);

        $translationCategorySource = $translationCategorySources[0];
        $this->assertInstanceOf(CategorySource::class, $translationCategorySource);

        return $translationCategorySource;
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
