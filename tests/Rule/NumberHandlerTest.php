<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use stdClass;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\NumberHandler;
use Yiisoft\Validator\RuleHandlerInterface;

final class NumberHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $rule = new Number();
        $ruleInteger = new Number(asInteger: true);

        $notANumberMessage = 'Value must be a number.';
        $notAnIntegerMessage = 'Value must be an integer.';

        return [
            [$rule, '12:45', [new Error($notANumberMessage)]],

            [$rule, false, [new Error($notANumberMessage)]],
            [$rule, true, [new Error($notANumberMessage)]],

            [$rule, 'e12', [new Error($notANumberMessage)]],
            [$rule, '-e3', [new Error($notANumberMessage)]],
            [$rule, '-4.534-e-12', [new Error($notANumberMessage)]],
            [$rule, '12.23^4', [new Error($notANumberMessage)]],
            [$rule, '43^32', [new Error($notANumberMessage)]],

            [$rule, [1, 2, 3], [new Error($notANumberMessage)]],
            [$rule, new stdClass(), [new Error($notANumberMessage)]],
            [$rule, fopen('php://stdin', 'rb'), [new Error($notANumberMessage)]],

            [$ruleInteger, 25.45, [new Error($notAnIntegerMessage)]],
            [$ruleInteger, '25,45', [new Error($notAnIntegerMessage)]],
            [$ruleInteger, '0x14', [new Error($notAnIntegerMessage)]],

            [$ruleInteger, '-1.23', [new Error($notAnIntegerMessage)]],
            [$ruleInteger, '-4.423e-12', [new Error($notAnIntegerMessage)]],
            [$ruleInteger, '12E3', [new Error($notAnIntegerMessage)]],
            [$ruleInteger, 'e12', [new Error($notAnIntegerMessage)]],
            [$ruleInteger, '-e3', [new Error($notAnIntegerMessage)]],
            [$ruleInteger, '-4.534-e-12', [new Error($notAnIntegerMessage)]],
            [$ruleInteger, '12.23^4', [new Error($notAnIntegerMessage)]],

            [new Number(min: 1), -1, [new Error('Value must be no less than 1.')]],
            [new Number(min: 1), '22e-12', [new Error('Value must be no less than 1.')]],

            [new Number(asInteger: true, min: 1), -1, [new Error('Value must be no less than 1.')]],
            [new Number(asInteger: true, min: 1), '22e-12', [new Error($notAnIntegerMessage)]],
            [new Number(max: 1.25), 1.5, [new Error('Value must be no greater than 1.25.')]],

            // TODO: fix wrong message
            [new Number(asInteger: true, max: 1.25), 1.5, [new Error($notAnIntegerMessage)]],
            [new Number(asInteger: true, max: 1.25), '22e-12', [new Error($notAnIntegerMessage)]],
            [new Number(asInteger: true, max: 1.25), '125e-2', [new Error($notAnIntegerMessage)]],

            [new Number(min: -10, max: 20), -11, [new Error('Value must be no less than -10.')]],
            [new Number(min: -10, max: 20), 21, [new Error('Value must be no greater than 20.')]],
            [new Number(asInteger: true, min: -10, max: 20), -11, [new Error('Value must be no less than -10.')]],
            [new Number(asInteger: true, min: -10, max: 20), 22, [new Error('Value must be no greater than 20.')]],
            [new Number(asInteger: true, min: -10, max: 20), '20e-1', [new Error($notAnIntegerMessage)]],
        ];
    }

    public function passedValidationProvider(): array
    {
        $rule = new Number();
        $ruleInteger = new Number(asInteger: true);

        return [
            [$rule, 20],
            [$rule, 0],
            [$rule, .5],
            [$rule, -20],
            [$rule, '20'],
            [$rule, 25.45],
            [$rule, '25,45'],

            [$rule, '-1.23'],
            [$rule, '-4.423e-12'],
            [$rule, '12E3'],

            [$ruleInteger, 20],
            [$ruleInteger, 0],
            [$ruleInteger, '20'],
            [$ruleInteger, '020'],
            [$ruleInteger, 0x14],
            [$rule, '5.5e1'],

            [new Number(min: 1), 1],
            [new Number(min: 1), PHP_INT_MAX + 1],

            [new Number(asInteger: true, min: 1), 1],

            [new Number(max: 1), 1],
            [new Number(max: 1.25), 1],
            [new Number(max: 1.25), '22e-12'],
            [new Number(max: 1.25), '125e-2'],
            [new Number(asInteger: true, max: 1.25), 1],

            [new Number(min: -10, max: 20), 0],
            [new Number(min: -10, max: 20), -10],

            [new Number(asInteger: true, min: -10, max: 20), 0],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [new Number(min: 5, tooSmallMessage: 'Value is too small.'), 0, [new Error('Value is too small.')]],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new NumberHandler($this->getTranslator());
    }
}
