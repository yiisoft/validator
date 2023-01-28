<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Helper;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Helper\ObjectParser;
use Yiisoft\Validator\Tests\Support\Data\SimpleDto;
use Yiisoft\Validator\Tests\Support\Rule\CustomUrlRuleSet;

final class ObjectParserTest extends TestCase
{
    public function testInvalidSource(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Class "non-exist-class" not found.');
        new ObjectParser('non-exist-class');
    }

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
                ['a' => 4, 'c' => 'hello'],
                new class () {
                    public int $a = 4;
                    public static int $b = 2;
                    public string $c = 'hello';
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

    public function testDataWithClassString(): void
    {
        $parser = new ObjectParser(SimpleDto::class);

        $this->assertSame([], $parser->getData());
        $this->assertNull($parser->getAttributeValue('a'));
        $this->assertNull($parser->getAttributeValue('x'));
        $this->assertFalse($parser->hasAttribute('a'));
        $this->assertFalse($parser->hasAttribute('x'));
    }

    public function testGetRulesExpectingAttributeInheritance(): void
    {
        $object = new class () {
            #[CustomUrlRuleSet]
            public string $url;
        };
        $parser = new ObjectParser($object);

        $this->expectError();

        $className = CustomUrlRuleSet::class;
        $this->expectErrorMessage("Attempting to use non-attribute class \"$className\" as attribute");

        $parser->getRules();
    }
}
