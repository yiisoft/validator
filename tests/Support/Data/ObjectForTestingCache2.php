<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Tests\Helper\ObjectParserTest;

/**
 * Should be used only in one test ({@see ObjectParserTest::testCache()}).
 */
final class ObjectForTestingCache2
{
    #[Required]
    public string $d = '';

    #[Number(min: 5)]
    protected int $e = 7;

    #[Number(max: 6)]
    private int $f = 8;
}
