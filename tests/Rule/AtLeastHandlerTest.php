<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\Rule\AtLeastHandler;
use Yiisoft\Validator\Tests\Stub\FakeValidatorFactory;
use Yiisoft\Validator\Tests\Stub\TranslatorFactory;
use Yiisoft\Validator\ValidationContext;

final class AtLeastHandlerTest extends TestCase
{
    public function testDifferentRule(): void
    {
        $handler = $this->createHandler();
        $context = new ValidationContext(FakeValidatorFactory::make(), null);

        $this->expectExceptionMessageMatches('/' . AtLeastHandler::class . '/');
        $this->expectExceptionMessageMatches('/' . stdClass::class . '/');
        $handler->validate('value', new stdClass(), $context);
    }

    private function createHandler(): AtLeastHandler
    {
        return new AtLeastHandler(
            (new TranslatorFactory())->create()
        );
    }
}
