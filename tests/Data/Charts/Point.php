<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Data\Charts;

use Yiisoft\Validator\Attribute\HasOne;
use Yiisoft\Validator\Rule\Count\Count;
use Yiisoft\Validator\Rule\Each\Each;
use Yiisoft\Validator\Rule\Nested\Nested;
use Yiisoft\Validator\Rule\Number\Number;

final class Point
{
    #[Nested(errorWhenPropertyPathIsNotFound: true, propertyPathIsNotFoundMessage: 'Custom message 4.')]
    #[HasOne(Coordinates::class)]
    private $coordinates;
    #[Count(exactly: 3)]
    #[Each(incorrectInputMessage: 'Custom message 5.', message: 'Custom message 6.')]
    #[Number(min: 0, max: 255)]
    private array $rgb;
}
