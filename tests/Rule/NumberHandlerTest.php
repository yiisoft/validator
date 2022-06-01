<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use stdClass;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\NumberHandler;
use Yiisoft\Validator\Rule\RuleHandlerInterface;

final class NumberHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $rule = new Number();
        $ruleInteger = new Number(asInteger: true);

        return [
            [$rule, '12:45', [new Error($this->formatMessage($rule->getNotANumberMessage(), ['value' => '12:45']))]],

            [$rule, false, [new Error($this->formatMessage($rule->getNotANumberMessage(), ['value' => false]))]],
            [$rule, true, [new Error($this->formatMessage($rule->getNotANumberMessage(), ['value' => true]))]],

            [$rule, 'e12', [new Error($this->formatMessage($rule->getNotANumberMessage(), ['value' => 'e12']))]],
            [$rule, '-e3', [new Error($this->formatMessage($rule->getNotANumberMessage(), ['value' => '-e3']))]],
            [$rule, '-4.534-e-12', [new Error($this->formatMessage($rule->getNotANumberMessage(), ['value' => '-4.534-e-12']))]],
            [$rule, '12.23^4', [new Error($this->formatMessage($rule->getNotANumberMessage(), ['value' => '12.23^4']))]],
            [$rule, '43^32', [new Error($this->formatMessage($rule->getNotANumberMessage(), ['value' => '43^32']))]],

            [$rule, [1, 2, 3], [new Error($this->formatMessage($rule->getNotANumberMessage(), ['value' => [1, 2, 3]]))]],
            [$rule, $object = new stdClass(), [new Error($this->formatMessage($rule->getNotANumberMessage(), ['value' => $object]))]],
            [$rule, $resource = fopen('php://stdin', 'rb'), [new Error($this->formatMessage($rule->getNotANumberMessage(), ['value' => $resource]))]],

            [$ruleInteger, 25.45, [new Error($this->formatMessage($ruleInteger->getNotANumberMessage(), ['value' => 25.45]))]],
            [$ruleInteger, '25,45', [new Error($this->formatMessage($ruleInteger->getNotANumberMessage(), ['value' => '25,45']))]],
            [$ruleInteger, '0x14', [new Error($this->formatMessage($ruleInteger->getNotANumberMessage(), ['value' => '0x14']))]],

            [$ruleInteger, '-1.23', [new Error($this->formatMessage($ruleInteger->getNotANumberMessage(), ['value' => '-1.23']))]],
            [$ruleInteger, '-4.423e-12', [new Error($this->formatMessage($ruleInteger->getNotANumberMessage(), ['value' => '-4.423e-12']))]],
            [$ruleInteger, '12E3', [new Error($this->formatMessage($ruleInteger->getNotANumberMessage(), ['value' => '12E3']))]],
            [$ruleInteger, 'e12', [new Error($this->formatMessage($ruleInteger->getNotANumberMessage(), ['value' => 'e12']))]],
            [$ruleInteger, '-e3', [new Error($this->formatMessage($ruleInteger->getNotANumberMessage(), ['value' => '-e3']))]],
            [$ruleInteger, '-4.534-e-12', [new Error($this->formatMessage($ruleInteger->getNotANumberMessage(), ['value' => '-4.534-e-12']))]],
            [$ruleInteger, '12.23^4', [new Error($this->formatMessage($ruleInteger->getNotANumberMessage(), ['value' => '12.23^4']))]],


            [new Number(min: 1), -1, [new Error($this->formatMessage($rule->getTooSmallMessage(), ['min' => 1]))]],
            [new Number(min: 1), '22e-12', [new Error($this->formatMessage($rule->getTooSmallMessage(), ['min' => 1]))]],


            [new Number(asInteger: true, min: 1), -1, [new Error($this->formatMessage($rule->getTooSmallMessage(), ['min' => 1]))]],
            [new Number(asInteger: true, min: 1), '22e-12', [new Error($this->formatMessage($ruleInteger->getNotANumberMessage(), ['value' => '22e-12']))]],
            [new Number(max: 1.25), 1.5, [new Error($this->formatMessage($ruleInteger->getTooBigMessage(), ['max' => 1.25]))]],

            // TODO: fix wrong message
            [new Number(asInteger: true, max: 1.25), 1.5, [new Error($this->formatMessage($ruleInteger->getNotANumberMessage(), ['value' => 1.5]))]],
            [new Number(asInteger: true, max: 1.25), '22e-12', [new Error($this->formatMessage($ruleInteger->getNotANumberMessage(), ['value' => '22e-12']))]],
            [new Number(asInteger: true, max: 1.25), '125e-2', [new Error($this->formatMessage($ruleInteger->getNotANumberMessage(), ['value' => '125e-2']))]],


            [new Number(min: -10, max: 20), -11, [new Error($this->formatMessage($ruleInteger->getTooSmallMessage(), ['min' => -10]))]],
            [new Number(min: -10, max: 20), 21, [new Error($this->formatMessage($ruleInteger->getTooBigMessage(), ['max' => 20]))]],
            [new Number(asInteger: true, min: -10, max: 20), -11, [new Error($this->formatMessage($ruleInteger->getTooSmallMessage(), ['min' => -10]))]],
            [new Number(asInteger: true, min: -10, max: 20), 22, [new Error($this->formatMessage($ruleInteger->getTooBigMessage(), ['max' => 20]))]],
            [new Number(asInteger: true, min: -10, max: 20), '20e-1', [new Error($this->formatMessage($ruleInteger->getNotANumberMessage(), ['value' => '20e-1']))]],
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
            [
                new Number(min: 5, tooSmallMessage: 'Value is too small.'),
                0,
                [new Error('Value is too small.')],
            ],
        ];
    }

    protected function getValidator(): RuleHandlerInterface
    {
        return new NumberHandler();
    }
}
