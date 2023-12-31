<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Helper;

use Error;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionObject;
use Yiisoft\Validator\Helper\ObjectParser;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Tests\Support\Data\ObjectForTestingCache1;
use Yiisoft\Validator\Tests\Support\Data\ObjectForTestingCache2;
use Yiisoft\Validator\Tests\Support\Data\ObjectForTestingDisabledCache;
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
        $parser1 = new ObjectParser(new ObjectForTestingCache1());
        $reflectionParser = new ReflectionObject($parser1);

        $cacheProperty = $reflectionParser->getProperty('cache');
        if (PHP_VERSION_ID < 80100) {
            $cacheProperty->setAccessible(true);
        }

        $cacheKey1 = 'Yiisoft\Validator\Tests\Support\Data\ObjectForTestingCache1_7_0';
        $this->assertArrayNotHasKey($cacheKey1, $cacheProperty->getValue());

        $expectedRules1 = [
            'a' => [new Required()],
            'b' => [new Number(min: 1)],
            'c' => [new Number(max: 2)],
        ];
        $expectedLabels1 = ['c' => 'd'];
        $rules1 = $parser1->getRules();
        $labels1 = $parser1->getLabels();
        $this->assertEquals($expectedRules1, $rules1);
        $cache = $cacheProperty->getValue();
        $this->assertArrayHasKey($cacheKey1, $cache);
        $this->assertArrayHasKey('rules', $cache[$cacheKey1]);
        $this->assertArrayHasKey('reflectionProperties', $cache[$cacheKey1]);
        $this->assertArrayHasKey('reflectionSource', $cache[$cacheKey1]);
        $this->assertArrayHasKey('labels', $cache[$cacheKey1]);
        $this->assertSame($rules1, $parser1->getRules());
        $this->assertSame($labels1, $expectedLabels1);

        $parser2 = new ObjectParser(new ObjectForTestingCache2());
        $cacheKey2 = 'Yiisoft\Validator\Tests\Support\Data\ObjectForTestingCache2_7_0';
        $cache = $cacheProperty->getValue();
        $this->assertArrayHasKey($cacheKey1, $cache);
        $this->assertArrayNotHasKey($cacheKey2, $cache);

        $reflectionProperties2 = $parser2->getReflectionProperties();
        $cache = $cacheProperty->getValue();
        $this->assertArrayHasKey($cacheKey1, $cache);
        $this->assertArrayHasKey($cacheKey2, $cache);
        $this->assertArrayNotHasKey('rules', $cache[$cacheKey2]);
        $this->assertArrayHasKey('reflectionProperties', $cache[$cacheKey2]);
        $this->assertArrayHasKey('reflectionSource', $cache[$cacheKey2]);
        $this->assertSame($reflectionProperties2, $parser2->getReflectionProperties());

        $expectedRules2 = [
            'd' => [new Required()],
            'e' => [new Number(min: 5)],
            'f' => [new Number(max: 6)],
        ];
        $rules2 = $parser2->getRules();
        $this->assertEquals($expectedRules2, $parser2->getRules());
        $this->assertArrayHasKey('rules', $cacheProperty->getValue()[$cacheKey2]);
        $this->assertSame($rules2, $parser2->getRules());
        $this->assertSame($rules1, $parser1->getRules());
    }

    public function testDisabledCache(): void
    {
        $parser = new ObjectParser(new ObjectForTestingDisabledCache(), useCache: false);
        $reflectionParser = new ReflectionObject($parser);

        $cacheProperty = $reflectionParser->getProperty('cache');
        if (PHP_VERSION_ID < 80100) {
            $cacheProperty->setAccessible(true);
        }

        $cacheKey = 'Yiisoft\Validator\Tests\Support\Data\ObjectForTestingDisabledCache_7_0';
        $this->assertArrayNotHasKey($cacheKey, $cacheProperty->getValue());

        $expectedRules = [
            'a' => [new Required()],
            'b' => [new Number(min: 1)],
            'c' => [new Number(max: 2)],
        ];
        $expectedLabels = ['c' => 'label'];
        $rules = $parser->getRules();
        $labels = $parser->getLabels();
        $this->assertEquals($expectedRules, $rules);
        $this->assertSame($expectedLabels, $labels);
        $this->assertArrayNotHasKey($cacheKey, $cacheProperty->getValue());
        $this->assertEquals($expectedRules, $parser->getRules());
        $this->assertSame($expectedLabels, $parser->getLabels());
        $this->assertNotSame($rules, $parser->getRules());
        $this->assertArrayNotHasKey($cacheKey, $cacheProperty->getValue());
    }
}
