<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Helper;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Helper\ObjectParser;

final class ObjectParserTest extends TestCase
{
    public function dataSkipStaticProperties(): array
    {
        return [
            [
                ['a' => 4, 'b' => 2],
                new class () {
                    public int $a = 4;
                    public static int $b = 2;
                },
                false,
            ],
            [
                ['a' => 4],
                new class () {
                    public int $a = 4;
                    public static int $b = 2;
                },
                true,
            ],
        ];
    }

    /**
     * @dataProvider dataSkipStaticProperties
     */
    public function testSkipStaticProperties(array $expectedData, object $object, bool $skipStaticProperties): void
    {
        $parser = new ObjectParser($object, skipStaticProperties: $skipStaticProperties);

        $this->assertSame($expectedData, $parser->getData());
    }

    public function testSkipStaticPropertiesDefault(): void
    {
        $object = new class () {
            public int $a = 4;
            public static int $b = 2;
        };

        $parser = new ObjectParser($object);

        $this->assertSame(['a' => 4, 'b' => 2], $parser->getData());
    }
}
