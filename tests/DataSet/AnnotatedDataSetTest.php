<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\DataSet\AnnotatedDataSet;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Tests\Data\Charts\ChartsData;
use Yiisoft\Validator\Tests\Data\Charts\CustomFormatter;

final class AnnotatedDataSetTest extends TestCase
{
    public function testGetRules(): void
    {
        $dataSet = new AnnotatedDataSet(new ChartsData());
        $expectedRules = [
            'charts' => [
                new Each([
                    new Nested([
                        'points' => [
                            new Each([new Nested([
                                'coordinates' => new Nested(
                                    [
                                        'x' => [new Number(min: -10, max: 10, formatter: new CustomFormatter())],
                                        'y' => [new Number(min: -10, max: 10)],
                                    ],
                                    errorWhenPropertyPathIsNotFound: true,
                                    propertyPathIsNotFoundMessage: 'Custom message 4.'
                                ),
                                'rgb' => [
                                    new Each([
                                        new Number(min: 0, max: 255),
                                    ], incorrectInputMessage: 'Custom message 5.', message: 'Custom message 6.'),
                                ],
                            ])]),
                        ],
                    ], errorWhenPropertyPathIsNotFound: true, propertyPathIsNotFoundMessage: 'Custom message 3.'),
                ], incorrectInputMessage: 'Custom message 1.', message: 'Custom message 2.'),
            ],
        ];

        $this->assertEquals($expectedRules, $dataSet->getRules());
    }
}
