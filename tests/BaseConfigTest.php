<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Di\Container;
use Yiisoft\Di\ContainerConfig;

use function dirname;

abstract class BaseConfigTest extends TestCase
{
    final protected function createContainer(array|null $params = null): Container
    {
        $config = ContainerConfig::create()->withDefinitions($this->getCommonDefinitions($params));

        return new Container($config);
    }

    final protected function getCommonDefinitions(array|null $params): array
    {
        if ($params === null) {
            $params = $this->getParams();
        }

        return require dirname(__DIR__) . '/config/common.php';
    }

    final protected function getParams(): array
    {
        return require dirname(__DIR__) . '/config/params.php';
    }
}
