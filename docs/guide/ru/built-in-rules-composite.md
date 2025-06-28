# `Composite` - группировка нескольких правил валидации

`Composite` позволяет группировать несколько правил и конфигурировать общие
[варианты пропуска](conditional-validation.md), такие как `skipOnEmpty`,
`skipOnError` и `when`, для всего набора только один раз, а не повторять их
в каждом правиле:

```php
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;

new Composite(
    [
        new Length(max: 255),
        new Email(),
    ],
    skipOnEmpty: true,
);
```

## Повторное использование нескольких правил/одного правила с теми же параметрами

`Composite` — одно из немногих встроенных правил, которое не является
`final`. Это означает, что вы можете расширить его и переопределить метод
`getRules()`, чтобы создать повторно используемый набор правил:

```php
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;

final class UsernameRuleSet extends Composite
{
    public function getRules(): array
    {
        return [
            new Length(min: 2, max: 20),
            new Regex('~^[a-z_\-]*$~i'),
        ];
    }
}
```

И используйте его просто как обычное правило:

```php
use Yiisoft\Validator\Validator;

$result = (new Validator())->validate('John', new UsernameRuleSet());
```

Его также можно объединить с правилом [Nested](built-in-rules-nested.md) для
повторного использования правил для нескольких свойств:

```php
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;

final class CoordinatesRuleSet extends Composite
{
    public function getRules(): array
    {
        return [
            new Nested(
                'latitude' => new Number(min: -90, max: 90),
                'longitude' => new Number(min: -90, max: 90),
            ),
        ];
    }
}
```

Даже проблему повторного использования только одного правила с теми же
аргументами можно решить с помощью Composite:

```php
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Number;

final class ChartCoordinateRuleSet extends Composite
{
    public function getRules(): array
    {
        return [new Number(min: -10, max: 10)];
    }
}
```
