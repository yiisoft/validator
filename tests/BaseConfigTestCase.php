<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;

use function dirname;

abstract class BaseConfigTestCase extends TestCase
{
    final protected function createContainer(array|null $params = null): Container
    {
        $config = ContainerConfig::create()->withDefinitions($this->getContainerDefinitions($params));

        return new Container($config);
    }

    final protected function getContainerDefinitions(array|null $params): array
    {
        if ($params === null) {
            $params = $this->getParams();
        }

        return require dirname(__DIR__) . '/config/di.php';
    }

    final protected function getParams(): array
    {
        return require dirname(__DIR__) . '/config/params.php';
    }
}
