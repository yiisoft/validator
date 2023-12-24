<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Data;

use Yiisoft\Validator\Helper\Label;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Tests\Helper\ObjectParserTest;

/**
 * Should be used only in one test ({@see ObjectParserTest::testCache()}).
 */
final class ObjectForTestingCache1
{
    #[Required]
    public string $a = '';

    #[Number(min: 1)]
    protected int $b = 3;

    #[Label('d')]
    #[Number(max: 2)]
    private int $c = 4;
}
