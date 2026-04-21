# Валидация на стороне клиента

В отличие от Yii2, этот пакет не обеспечивает обработку правил валидации на
стороне клиента. Вероятно, это будет добавлено позже в виде другого
связанного пакета.

Однако существует возможность экспортировать параметры правил в виде массива
для передачи на сторону клиента с помощью класса `RulesDumper`:

- Поддерживаются множественные правила и вложенность правил.
- Если правило не предоставляет параметров, экспортируется только имя.
- Значения параметров, которые не могут быть сериализованы/воспроизведены на
стороне клиента (например, вызываемые объекты), исключаются либо полностью,
как `Callback::$callback`, либо частично, как `$skipOnEmpty`, если
поддерживаются несколько типов.

Учитывая встроенное правило `Length`:

```php
use Yiisoft\Validator\Helper\RulesDumper;
use Yiisoft\Validator\Rule\Length;

$rules = [  
    'name' => [  
        new Length(min: 4, max: 10),  
    ],  
];  
$options = RulesDumper::asArray($rules);
```

на выходе получим:

```php
[  
    'name' => [  
        [  
            'Yiisoft\Validator\Rule\Length',  
            'min' => 4,  
            'max' => 10,  
            'exactly' => null,  
            'lessThanMinMessage' => [  
                'template' => 'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.',  
                'parameters' => ['min' => 4],  
            ],  
            'greaterThanMaxMessage' => [  
                'template' => 'This value must contain at most {max, number} {max, plural, one{character} other{characters}}.',  
                'parameters' => ['max' => 10],  
            ],  
            'notExactlyMessage' => [  
                'template' => 'This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.',  
                'parameters' => ['exactly' => null],  
            ],  
            'incorrectInputMessage' => [  
                'template' => 'The value must be a string.',  
                'parameters' => [],  
            ],  
            'encoding' => 'UTF-8',  
            'skipOnEmpty' => false,  
            'skipOnError' => false,  
        ],
    ],  
],
```

Полученный массив, сериализованный как JSON, можно десериализовать обратно и
применить к реализации по вашему выбору.

## Структура экспортируемых опций

Вот некоторые особенности структуры правил:

- Индексация правил по именам свойств сохраняется.
- Первый элемент правила всегда представляет собой имя правила с
  целочисленным индексом `0`.
- Остальные элементы правила представляют собой пары «ключ-значение», где
  ключ — это имя параметра, а значение — соответствующее значение параметра.
- Для сложных правил, таких как `Composite`, `Each` и `Nested`, параметры
  дочерних правил находятся под ключом `rules`.

Обратите внимание, что сообщения об ошибках имеют особую структуру:

```php
[
    'lessThanMinMessage' => [  
        'template' => 'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.',  
        'parameters' => ['min' => 4],  
    ],
];
```

Он остается неизменным независимо от наличия заполнителей и параметров:

```php
'message' => [
    'template' => 'Value is invalid.',
    'parameters' => [],
],
```
