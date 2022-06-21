<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Handler\Container\Tests;

use Yiisoft\Test\Support\Container\SimpleContainer;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Exception\RuleHandlerInterfaceNotImplementedException;
use Yiisoft\Validator\Exception\RuleHandlerNotFoundException;
use Yiisoft\Validator\ContainerRuleHandlerResolver;
use Yiisoft\Validator\Tests\Stub\PiHandler;
use Yiisoft\Validator\Rule\RuleHandlerInterface;

class RuleHandlerContainerTest extends TestCase
{
    public function testCreate(): void
    {
        $handlersContainer = new ContainerRuleHandlerResolver(new SimpleContainer([
            PiHandler::class => new PiHandler(),
        ]));

        $handler = $handlersContainer->resolve(PiHandler::class);

        $this->assertInstanceOf(PiHandler::class, $handler);
    }

    public function testNotFound(): void
    {
        $handlersContainer = new ContainerRuleHandlerResolver(new SimpleContainer());

        $this->expectException(RuleHandlerNotFoundException::class);
        $this->expectExceptionMessage(
            'Handler was not found for "not-exists-handler" rule or unresolved "not-exists-handler" class.'
        );
        $this->expectExceptionCode(0);
        $handlersContainer->resolve('not-exists-handler');
    }

    public function testNotRuleInterface(): void
    {
        $handlersContainer = new ContainerRuleHandlerResolver(new SimpleContainer(['handler' => new \stdClass()]));

        $this->expectException(RuleHandlerInterfaceNotImplementedException::class);
        $this->expectExceptionMessage('Handler "handler" should implement "' . RuleHandlerInterface::class . '".');
        $this->expectExceptionCode(0);
        $handlersContainer->resolve('handler');
    }
}
