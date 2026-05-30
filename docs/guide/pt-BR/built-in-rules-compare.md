# `Compare` - comparando o valor validado com o valor alvo

## Usando com objetos [`DateTime`]

### Uso básico

```php
use DateTime;
use Yiisoft\Validator\Rule\CompareType;
use Yiisoft\Validator\Rule\GreaterThanOrEqual;

$rules = [
    'date_of_birth' => new GreaterThanOrEqual(new DateTime('1900-01-01'), type: CompareType::ORIGINAL),
];
```

### Limites dinâmicos

```php
use DateTime;
use DateInterval;
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
