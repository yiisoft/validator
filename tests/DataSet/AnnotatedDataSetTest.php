<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\DataSet;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\DataSet\AnnotatedDataSet;
use Yiisoft\Validator\Tests\Data\ChartsData;

/**
 * @requires PHP >= 8.0
 */
final class AnnotatedDataSetTest extends TestCase
{
    public function testGetRules(): void
    {
        $dataSet = new AnnotatedDataSet(new ChartsData());
        $rules = (array) $dataSet->getRules();

        $this->assertEquals(['charts'], array_keys($rules));
        $this->assertEquals([0], array_keys($rules['charts']));

        $actualOptions = $dataSet->getRules()['charts'][0]->getOptions();
        $replacedValuePaths = [
            [0, 'points', 0, 0, 'coordinates', 'x', 0],
            [0, 'points', 0, 0, 'coordinates', 'y', 0],
            [0, 'points', 0, 0, 'rgb', 0],
        ];
        $checkedKeys = ['0', 'min', 'max', 'skipOnError'];
        foreach ($replacedValuePaths as $replacedValuePath) {
            $value = ArrayHelper::getValue($actualOptions, $replacedValuePath);
            $value = ArrayHelper::filter($value, $checkedKeys);

            ArrayHelper::setValue($actualOptions, $replacedValuePath, $value);
        }

        $expectedOptions = [
            [
                'nested',
                'points' => [
                    [
                        [
                            'nested',
                            'coordinates' => [
                                'x' => [
                                    [
                                        'min' => -10,
                                        'max' => 10,
                                        'skipOnError' => true,
                                    ],
                                ],
                                'y' => [
                                    [
                                        'min' => -10,
                                        'max' => 10,
                                        'skipOnError' => true,
                                    ],
                                ],
                            ],
                            'rgb' => [
                                [
                                    'number',
                                    'min' => 0,
                                    'max' => 255,
                                    'skipOnError' => false,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->assertEquals($expectedOptions, $actualOptions);
    }
}
