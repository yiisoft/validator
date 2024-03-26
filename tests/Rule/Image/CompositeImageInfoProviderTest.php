<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Image;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Image\CompositeImageInfoProvider;
use Yiisoft\Validator\Rule\Image\ImageInfo;
use Yiisoft\Validator\Rule\Image\NativeImageInfoProvider;

final class CompositeImageInfoProviderTest extends TestCase
{
    public function testWithoutProviders(): void
    {
        $provider = new CompositeImageInfoProvider();

        $result = $provider->get(__DIR__ . '/16x18.jpg');

        $this->assertNull($result);
    }

    public function testWithOneProvider(): void
    {
        $provider = new CompositeImageInfoProvider(new NativeImageInfoProvider());

        $result = $provider->get(__DIR__ . '/16x18.jpg');

        $this->assertInstanceOf(ImageInfo::class, $result);
        $this->assertSame(16, $result->getWidth());
        $this->assertSame(18, $result->getHeight());
    }

    public function testWithTwoProviders(): void
    {
        $provider = new CompositeImageInfoProvider(
            new StubImageInfoProvider(),
            new NativeImageInfoProvider(),
        );

        $result = $provider->get(__DIR__ . '/16x18.jpg');

        $this->assertInstanceOf(ImageInfo::class, $result);
        $this->assertSame(16, $result->getWidth());
        $this->assertSame(18, $result->getHeight());
    }

    public function testWithTwoProviders2(): void
    {
        $provider = new CompositeImageInfoProvider(
            new StubImageInfoProvider(new ImageInfo(10, 15)),
            new NativeImageInfoProvider(),
        );

        $result = $provider->get(__DIR__ . '/16x18.jpg');

        $this->assertInstanceOf(ImageInfo::class, $result);
        $this->assertSame(10, $result->getWidth());
        $this->assertSame(15, $result->getHeight());
    }

    public function testWithTwoProviders3(): void
    {
        $provider = new CompositeImageInfoProvider(
            new StubImageInfoProvider(),
            new StubImageInfoProvider(),
        );

        $result = $provider->get(__DIR__ . '/16x18.jpg');

        $this->assertNull($result);
    }
}
