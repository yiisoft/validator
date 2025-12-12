<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Nested\NestedPropertyTranslator;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Validator;

use function PHPUnit\Framework\assertSame;

final class NestedPropertyTranslatorTest extends TestCase
{
    public function testBase(): void
    {
        $form = new MainForm(new SubForm());
        $validator = new Validator();

        $result = $validator->validate($form);

        assertSame(
            ['Телефон must contain at least 5 characters.'],
            $result->getErrorMessages(),
        );
    }
}
