<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Attribute;

use PHPUnit\Framework\TestCase;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Tests\Data\ChartsData;

/**
 * @requires PHP >= 8.0
 */
final class AttributeTraitTest extends TestCase
{
    public function testGetRule()
    {
        $rule = (new ChartsData())->getRule();
        $actualOptions = $rule->getOptions();
        $replacedValuePaths = [
            'charts.0.dots.0.coordinates.x.0',
            'charts.0.dots.0.coordinates.y.0',
            'charts.0.dots.0.rgb.0',
        ];
        $checkedKeys = ['0', 'min', 'max', 'skipOnError'];
        foreach ($replacedValuePaths as $replacedValuePath) {
            $value = ArrayHelper::getValueByPath($actualOptions, $replacedValuePath);
            $value = ArrayHelper::filter($value, $checkedKeys);

            ArrayHelper::setValueByPath($actualOptions, $replacedValuePath, $value);
        }

        $expectedOptions = [
            'charts' => [
                [
                    'nested',
                    'dots' => [
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
