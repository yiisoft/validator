<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\RuleHandlerResolver;

use Yiisoft\Validator\Rule\Image\ImageHandler;
use Yiisoft\Validator\Rule\Image\ImageInfo;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleHandlerResolverInterface;
use Yiisoft\Validator\Tests\Rule\Image\StubImageInfoProvider;

final class ImageHandlerContainer implements RuleHandlerResolverInterface
{
    public function __construct(private ImageInfo $imageInfo)
    {
    }

    public function resolve(string $name): RuleHandlerInterface
    {
        return new ImageHandler(new StubImageInfoProvider($this->imageInfo));
    }
}
