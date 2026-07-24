# `Callback` - обертка вокруг `вызываемого выражения`

Это правило позволяет проверять текущее значение свойства (но не только его) с помощью произвольного условия внутри

вызываемой функции. Преимущество заключается в том, что нет необходимости
создавать отдельное пользовательское правило и обработчик.

Условие может находиться в:

- Отдельная вызываемая функция.
- Вызываемый класс.
- Метод DTO (объекта передачи данных).

Недостатком использования отдельных функций и методов DTO является
отсутствие возможности повторного использования. Поэтому они в основном
полезны в определенных не повторяющихся конкретных ситуациях. Повторное
использование можно обеспечить с помощью вызываемых классов, но в
зависимости от других факторов (например, необходимости дополнительных
параметров) может оказаться целесообразным вместо этого создать полноценное
[пользовательское правило](creating-custom-rules.md) с отдельным
обработчиком.

Сигнатура функции обратного вызова выглядит следующим образом:

```php
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\ValidationContext;

function (mixed $value, Callback $rule, ValidationContext $context): Result;
```

где:

- `$value` проверяемое значение;
- `$rule` это ссылка на исходное `Callback` правило;
- `$context` контекст валидации;
- возвращаемое значение представляет собой экземпляр результата проверки,
  содержащий или не содержащий ошибки.

## Использование как функции

Пример передачи вызываемой функции в правило  `Callback`:

```php
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\ValidationContext;

new Callback(
    static function (mixed $value, Callback $rule, ValidationContext $context): Result {
        // Фактический код проверки.
        
        return new Result();
    },
);
```

## Примеры

### Проверка значения

Правило `Callback` можно использовать для добавления проверки, отсутствующей
во встроенных правилах, для значения одного свойства. Ниже приведен пример,
подтверждающий, что значение является допустимой строкой
[YAML](https://en.wikipedia.org/wiki/YAML) (дополнительно требуется  `yaml`
модуль для PHP):

```php
use Exception;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;

new Callback(
    static function (mixed $value): Result {
        if (!is_string($value)) {
            return (new Result())->addError('The value must be a string.');
        }

        $notYamlMessage = 'This value is not a valid YAML.';

        try {
            $data = yaml_parse($value);
        } catch (Exception $e) {
            return (new Result())->addError($notYamlMessage);
        }

        if ($data === false) {
            return (new Result())->addError($notYamlMessage);
        }

        return new Result();
    },
);
```

> **Примечание:** Обработка непроверенного пользовательского ввода с помощью `yaml_parse()` может быть опасной при определенных настройках.
> Для получения более подробной информации обратитесь к [документации `yaml_parse()`](https://www.php.net/manual/en/function.yaml-parse.php).

### Использование контекста валидации для проверки нескольких свойств, зависящих друг от друга

В приведенном ниже примере три угла проверяются на соответствие
градусам,чтобы сформировался корректный треугольник:

```php
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\ValidationContext;

$rules = [
    'angleA' => [
        new Required(),
        new Integer(),
    ],
    'angleB' => [
        new Required(),
        new Integer(),
    ],
    'angleC' => [
        new Required(),
        new Integer(),
    ],

    new Callback(
        static function (mixed $value, Callback $rule, ValidationContext $context): Result {
            $angleA = $context->getDataSet()->getPropertyValue('angleA');
            $angleB = $context->getDataSet()->getPropertyValue('angleB');
            $angleC = $context->getDataSet()->getPropertyValue('angleC');
            $sum = $angleA + $angleB + $angleC;
            
            if ($sum <= 0) {
                return (new Result())->addError('The angles\' sum can\'t be negative.');
            } 
            
            if ($sum > 180) {
                return (new Result())->addError('The angles\' sum can\'t be greater than 180 degrees.');
            }
            
            return new Result();
        }
    ),
];
```

### Замена шаблонного кода отдельными правилами и `when`

Однако в некоторых случаях использование контекста валидации может привести
к появлению шаблонного кода:

```php
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\ValidationContext;

static function (mixed $value, Callback $rule, ValidationContext $context): Result {
    if ($context->getDataSet()->getPropertyValue('married') === false) {
        return new Result();
    }
    
    $spouseAge = $context->getDataSet()->getPropertyValue('spouseAge');
    if ($spouseAge === null) {
        return (new Result())->addError('Spouse age is required.');
    }
    
    if (!is_int($spouseAge)) {
        return (new Result())->addError('Spouse age must be an integer.');
    }
    
    if ($spouseAge < 18 || $spouseAge > 100) {
        return (new Result())->addError('Spouse age must be between 18 and 100.');
    }        
    
    return new Result();
};
```

Их можно переписать, используя несколько правил и условную проверку, что
сделает код более интуитивно понятным. Можно использовать встроенные правила
там где это возможно:

```php
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\ValidationContext;

$rules = [
    'married' => new BooleanValue(),
    'spouseAge' => new Integer(
        min: 18,
        max: 100,
        when: static function (mixed $value, ValidationContext $context): bool {
            return $context->getDataSet()->getPropertyValue('married') === true;
        },
    ),
];
```

## Использование в качестве метода объекта

### Для свойства

При использовании в качестве PHP-атрибута установите метод объекта в
качестве функции обратного вызова:

```php
use Exception;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;

final class Config {
    public function __construct(
        #[Callback(method: 'validateYaml')]
        private string $yaml,
    ) {
    }

    private function validateYaml(mixed $value): Result 
    {
        if (!is_string($value)) {
            return (new Result())->addError('The value must be a string.');
        }
        
        $notYamlMessage = 'This value is not a valid YAML.';

        try {
            $data = yaml_parse($value);
        } catch (Exception $e) {
            return (new Result())->addError($notYamlMessage);
        }
        
        if ($data === false) {
            return (new Result())->addError($notYamlMessage);
        }

        return new Result();
    }
}
```

Сигнатура такая же, как и в обычной функции. Обратите внимание, что
ограничений на уровни видимости и статические модификаторы нет, все они
могут быть использованы (`public`, `protected`, `private`, `static`).

Использование аргумента `callback` вместо `method` с PHP-атрибутами
запрещено из-за текущих ограничений языка PHP (функция обратного вызова не
может находиться внутри PHP-атрибута).

### Для всего объекта

Также можно проверить весь объект:

```php
use Exception;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;

#[Callback(method: 'validate')]
final class Config {
    public function __construct(        
        private int $yaml,
    ) {
    }

    private function validate(): Result 
    {
        if (!is_string($this->yaml)) {
            return (new Result())->addError('The value must be a string.');
        }
        
        $notYamlMessage = 'This value is not a valid YAML.';

        try {
            $data = yaml_parse($this->yaml);
        } catch (Exception $e) {
            return (new Result())->addError($notYamlMessage);
        }
        
        if ($data === false) {
            return (new Result())->addError($notYamlMessage);
        }

        return new Result();
    }
}
```

Обратите внимание на использование значения свойства ($this->yaml) вместо аргумента метода ($value).

## Использование вызываемого класса

Класс, реализующий метод `__invoke()`, также можно использовать как
вызываемый объект (callable):

```php
use Exception;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\ValidationContext;

final class YamlCallback
{    
    public function __invoke(mixed $value): Result
    {
        if (!is_string($value)) {
            return (new Result())->addError('The value must be a string.');
        }
        
        $notYamlMessage = 'This value is not a valid YAML.';

        try {
            $data = yaml_parse($value);
        } catch (Exception $e) {
            return (new Result())->addError($notYamlMessage);
        }
        
        if ($data === false) {
            return (new Result())->addError($notYamlMessage);
        }

        return new Result();
    }
}
```

Сигнатура такая же, как и в обычной функции.

Использование в правилах (обратите внимание, что необходимо передать
экземпляр, а не имя класса):

```php
use Yiisoft\Validator\Rule\Callback;

$rules = [
    'yaml' => new Callback(new YamlCallback()),
];
``` 

## Сокращение при использовании в валидаторе

При использовании в валидаторе и настройках по умолчанию для `Callback`
правила, объявление правила можно опустить, поэтому достаточно просто
указать вызываемую функцию. Она будет автоматически нормализована перед
проверкой:

```php
use Exception;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'yaml' => static function (mixed $value): Result {
        if (!is_string($value)) {
            return (new Result())->addError('The value must be a string.');
        }

        $notYamlMessage = 'This value is not a valid YAML.';

        try {
            $data = yaml_parse($value);
        } catch (Exception $e) {
            return (new Result())->addError($notYamlMessage);
        }

        if ($data === false) {
            return (new Result())->addError($notYamlMessage);
        }

        return new Result();
    },
];
$result = (new Validator())->validate($data, $rules);
```

Или же его можно задать в массиве вместе с другими правилами:

```php
use Exception;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'yaml' => [
        new Required(),
        static function (mixed $value): Result {
            if (!is_string($value)) {
                return (new Result())->addError('The value must be a string.');
            }
        
            $notYamlMessage = 'This value is not a valid YAML.';
        
            try {
                $data = yaml_parse($value);
            } catch (Exception $e) {
                return (new Result())->addError($notYamlMessage);
            }
        
            if ($data === false) {
                return (new Result())->addError($notYamlMessage);
            }
        
            return new Result();
        },
    ],    
];
$result = (new Validator())->validate($data, $rules);
```
