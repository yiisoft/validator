# `Each` - применение одних и тех же правил для каждого элемента в наборе

Правило `Each` позволяет применять одинаковые правила к каждому элементу данных в наборе. Следующий пример показывает конфигурацию для валидации компонентов [модели RGB-цветов]:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;

new Each([
    new Integer(min: 0, max: 255),
]);
```

Комбинируя его с другим встроенным правилом `Количество`, мы можем быть уверены, что количество компонентов действительно равно 3:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;

$rules = [
    // Применяется ко всему набору.
    new Count(3),
    // Применяется к отдельному элементу набора.
    new Each(        
        // Для одиночных правил не требуется оборачивать его в массив / итерируемый объект.
        new Integer(min: 0, max: 255),
    ),
];
```

Проверяемые элементы данных не ограничиваются только "простыми значениями" - `Each` может использоваться как внутри правила `Nested` так и содержать его, имея отношения один-ко-многим и многие-ко-многим:

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
                    'rgb' => new Each([
                        new Count(3),
                        new Number(min: 0, max: 255),
                    ]),
                ]),
            ]),
        ]),
    ]),
]);
```

Дополнительную информацию об использовании с правилом `Nested` см. в [руководстве]

[модели RGB-цветов]: https://en.wikipedia.org/wiki/RGB_color_model
[руководстве]: built-in-rules-nested.md
