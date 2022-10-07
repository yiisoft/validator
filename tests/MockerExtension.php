<?php
declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Runner\BeforeTestHook;
use PHPUnit\Runner\BeforeFirstTestHook;
use Xepozz\InternalMocker\Mocker;
use Xepozz\InternalMocker\MockerState;

final class MockerExtension implements BeforeTestHook, BeforeFirstTestHook
{
    public function executeBeforeFirstTest(): void
    {
        self::load();
    }

    public static function load(): void
    {
        $mocks = [
            [
                'namespace' => 'Yiisoft\\Validator\\Rule',
                'name' => 'function_exists',
            ],
            [
                'namespace' => 'Yiisoft\\Validator\\Rule',
                'name' => 'idn_to_ascii',
            ],
            [
                'namespace' => 'Yiisoft\\Validator\\Rule',
                'name' => 'checkdnsrr',
                'result' => true,
                'default' => true,
            ],
            [
                'namespace' => 'Yiisoft\\Validator\\Tests\\Rule',
                'name' => 'extension_loaded',
            ],
        ];

        $mocker = new Mocker();
        $mocker->load($mocks);
        MockerState::saveState();
    }

    public function executeBeforeTest(string $test): void
    {
        MockerState::resetState();
    }
}
