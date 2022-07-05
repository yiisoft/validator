<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Data;

use Yiisoft\Validator\Attribute\HasMany;
use Yiisoft\Validator\Attribute\HasOne;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;

final class AnotherData
{
    #[Number(asInteger: false)]
    #[Number(asInteger: true)]
    private array $repeatable;

    #[Each(rules: [
        new Number(asInteger: false),
        new Number(asInteger: true),
    ])]
    #[Number(asInteger: false)]
    private array $eachWithAnother;

    #[Nested(rules: [
        new Number(asInteger: false),
        new Number(asInteger: true),
    ])]
    #[Nested(rules: [
        new Number(asInteger: false),
        new Number(asInteger: true),
    ])]
    private array $repeatableNested;

    #[Each(rules: [
        new HasOne(Post::class),
    ])]
    private array $posts;

    #[Nested(rules: [
        'posts' => new HasMany(Post::class),
    ])]
    private array $postsInPostsProperty;
}
