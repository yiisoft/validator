<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Data\Charts;

use Yiisoft\Validator\Attribute\HasMany;
use Yiisoft\Validator\Rule\Each\Each;
use Yiisoft\Validator\Rule\Nested\Nested;

final class ChartsData
{
    #[Each(incorrectInputMessage: 'Custom message 1.', message: 'Custom message 2.')]
    #[Nested(errorWhenPropertyPathIsNotFound: true, propertyPathIsNotFoundMessage: 'Custom message 3.')]
    #[HasMany(Chart::class)]
    private array $charts;
}
