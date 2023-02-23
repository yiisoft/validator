<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Helper;

use Error;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Helper\ObjectParser;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Tests\Support\Data\ObjectForTestingCache1;
use Yiisoft\Validator\Tests\Support\Data\ObjectForTestingCache2;
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

        $this->expectException(Error::class);

        $className = CustomUrlRuleSet::class;
        $this->expectExceptionMessage("Attempting to use non-attribute class \"$className\" as attribute");

        $parser->getRules();
    }

    public function testCache(): void
    {
        $parser = new ObjectParser(new ObjectForTestingCache1());
        $cacheKey1 = 'Yiisoft\Validator\Tests\Support\Data\ObjectForTestingCache1_7_0';
        $this->assertArrayNotHasKey($cacheKey1, ObjectParser::getCache());

        $expectedRules1 = [
            'a' => [new Required()],
            'b' => [new Number(min: 1)],
            'c' => [new Number(max: 2)],
        ];
        $this->assertEquals($expectedRules1, $parser->getRules());
        $this->assertArrayHasKey($cacheKey1, ObjectParser::getCache());
        $this->assertArrayHasKey('rules', ObjectParser::getCache()[$cacheKey1]);
        $this->assertArrayHasKey('reflectionProperties', ObjectParser::getCache()[$cacheKey1]);
        $this->assertArrayHasKey('reflectionSource', ObjectParser::getCache()[$cacheKey1]);
        $this->assertEquals($expectedRules1, $parser->getRules());

        $parser = new ObjectParser(new ObjectForTestingCache2());
        $cacheKey2 = 'Yiisoft\Validator\Tests\Support\Data\ObjectForTestingCache2_7_0';
        $this->assertArrayHasKey($cacheKey1, ObjectParser::getCache());
        $this->assertArrayNotHasKey($cacheKey2, ObjectParser::getCache());

        $parser->getReflectionProperties();
        $this->assertArrayHasKey($cacheKey1, ObjectParser::getCache());
        $this->assertArrayHasKey($cacheKey2, ObjectParser::getCache());
        $this->assertArrayNotHasKey('rules', ObjectParser::getCache()[$cacheKey2]);
        $this->assertArrayHasKey('reflectionProperties', ObjectParser::getCache()[$cacheKey2]);
        $this->assertArrayHasKey('reflectionSource', ObjectParser::getCache()[$cacheKey2]);

        $expectedRules2 = [
            'd' => [new Required()],
            'e' => [new Number(min: 5)],
            'f' => [new Number(max: 6)],
        ];
        $this->assertEquals($expectedRules2, $parser->getRules());
        $this->assertArrayHasKey('rules', ObjectParser::getCache()[$cacheKey2]);
        $this->assertEquals($expectedRules2, $parser->getRules());
    }
}
