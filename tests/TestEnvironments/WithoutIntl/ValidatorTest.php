<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\TestEnvironments\WithoutIntl;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Validator;

final class ValidatorTest extends TestCase
{
    public function testDefaultTranslatorWithoutIntl(): void
    {
        $data = ['number' => 3];
        $rules = [
            'number' => new Integer(
                max: 2,
                greaterThanMaxMessage: '{value, selectordinal, one{#-one} two{#-two} few{#-few} other{#-other}}',
            ),
        ];
        $validator = new Validator();

        $result = $validator->validate($data, $rules);
        $this->assertSame(['number' => ['3']], $result->getErrorMessagesIndexedByPath());
    }
}
