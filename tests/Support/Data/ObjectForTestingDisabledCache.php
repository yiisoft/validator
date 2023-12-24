<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Label;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Tests\Helper\ObjectParserTest;

/**
 * Should be used only in one test ({@see ObjectParserTest::testDisabledCache()}).
 */
final class ObjectForTestingDisabledCache
{
    #[Required]
    public string $a = '';

    #[Number(min: 1)]
    protected int $b = 3;

    #[Number(max: 2)]
    #[Label('label')]
    private int $c = 4;
}
