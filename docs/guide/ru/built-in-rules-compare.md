# `Compare` - сравнение валидируемого значения с целевым

The `Compare` rule and its shortcut classes validate a value by comparing it
with a target value or another property.

Available shortcut classes:

- `Equal` (`==`)
- `NotEqual` (`!=`)
- `GreaterThan` (`>`)
- `GreaterThanOrEqual` (`>=`)
- `LessThan` (`<`)
- `LessThanOrEqual` (`<=`)

## Comparison types

The `type` parameter controls how values are compared:

- `CompareType::NUMBER` — values are compared as numbers (default).
- `CompareType::STRING` — values are compared as strings, byte by byte.
- `CompareType::ORIGINAL` — values are compared as-is, without type
  casting. Required for `DateTime` objects.

## Comparing with a target value

```php
use Yiisoft\Validator\Rule\GreaterThanOrEqual;

$rules = [
    'age' => new GreaterThanOrEqual(18),
];
```

## Comparing with another property

Use the `targetProperty` parameter to compare against another property in
the same data set:

```php
use Yiisoft\Validator\Rule\Equal;

$rules = [
    'password_repeat' => new Equal(targetProperty: 'password'),
];
```

## Использование с объектом `DateTime`

### Базовое иcпользование

```php
use DateTime;
use Yiisoft\Validator\Rule\CompareType;
use Yiisoft\Validator\Rule\GreaterThanOrEqual;

$rules = [
    'date_of_birth' => new GreaterThanOrEqual(new DateTime('1900-01-01'), type: CompareType::ORIGINAL),
];
```

### Динамический диапазон

```php
use DateInterval;
use DateTime;
use Yiisoft\Validator\Rule\CompareType;
use Yiisoft\Validator\Rule\GreaterThan;
use Yiisoft\Validator\Rule\LessThan;

$rules = [
    'shipping_datetime' => [
        new GreaterThan(
            (new DateTime('now'))
                ->add(DateInterval::createFromDateString('1 day')),
            type: CompareType::ORIGINAL,
        ),
        new LessThan(
            (new DateTime('now'))
                ->add(DateInterval::createFromDateString('1 week')),
            type: CompareType::ORIGINAL,
        ),
    ],
];
```
