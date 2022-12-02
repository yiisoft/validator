<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\TestEnvironments\WithIntl;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Validator;

final class ValidatorTest extends TestCase
{
    public function testDefaultTranslatorWithIntl(): void
    {
        $data = ['number' => 3];
        $rules = [
            'number' => new Number(
                asInteger: true,
                max: 2,
                tooBigMessage: '{value, selectordinal, one{#-one} two{#-two} few{#-few} other{#-other}}',
            ),
        ];
        $validator = new Validator();

        $result = $validator->validate($data, $rules);
        $this->assertSame(['number' => ['3-few']], $result->getErrorMessagesIndexedByPath());
    }
}
