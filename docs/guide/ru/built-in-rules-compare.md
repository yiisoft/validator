# `Compare` - сравнение валидируемого значения с целевым

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
use DateTime;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\CompareType;
use Yiisoft\Validator\Rule\GreaterThanOrEqual;
use Yiisoft\Validator\Rule\LessThanOrEqual;

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
