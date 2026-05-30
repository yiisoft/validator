# `Each` - применение одних и тех же правил для каждого элемента в наборе

Правило `Each` позволяет применять одинаковые правила к каждому элементу
данных в наборе. Следующий пример показывает конфигурацию для валидации
компонентов [модели
RGB-цветов](https://en.wikipedia.org/wiki/RGB_color_model):

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;

new Each([
    new Integer(min: 0, max: 255),
]);
```

Комбинируя его с другим встроенным правилом `Count`, мы можем быть уверены,
что компонентов ровно три:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;

$rules = [
    // Применяется ко всему набору.
    new Count(3),
    // Применяется к отдельному элементу набора.
    new Each(        
        // Одиночные правила не требуется оборачивать в массив / итерируемый объект.
        new Integer(min: 0, max: 255),
    ),
];
```

## Stopping on first error

By default, `Each` validates all items in the set and collects all
errors. To stop validation at the first item that produces an error, use the
`stopOnError` parameter:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;

new Each(
    rules: [new Integer(min: 0, max: 255)],
    stopOnError: true,
);
```

## Accessing the current key

During validation of each item, the current iteration key is available
through the validation context parameter `Each::PARAMETER_EACH_KEY`. This
can be useful in `when` callbacks or custom rule handlers:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\ValidationContext;

// Inside a when callback or custom rule handler:
$currentKey = $context->getParameter(Each::PARAMETER_EACH_KEY);
```

## Using with `Nested`

Validated data items are not limited to only "simple" values — `Each` can be
used both within a `Nested` and contain `Nested` rule covering one-to-many
and many-to-many relations:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;

$rule = new Nested([
    'charts' => new Each([
        new Nested([
            'points' => new Each([
                new Nested([
                    'coordinates' => new Nested([
                        'x' => [new Number(min: -10, max: 10)],
                        'y' => [new Number(min: -10, max: 10)],
                    ]),
                    'rgb' => [
                        new Count(3),
                        new Each([new Number(min: 0, max: 255)]),
                    ],
                ]),
            ]),
        ]),
    ]),
]);
```

Дополнительную информацию об использовании с правилом `Nested` смотрите в
[руководстве](built-in-rules-nested.md).
