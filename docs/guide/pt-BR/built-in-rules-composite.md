# `Composite` - agrupando múltiplas regras de validação

`Composite` permite agrupar múltiplas regras e configurar as [opções de salto] comuns, como `skipOnEmpty`,
`skipOnError` e `when`, para todo o conjunto apenas uma vez em vez de repeti-los em cada regra:

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

## Reutilizando múltiplas regras/regra única com as mesmas opções

`Composite` é uma das poucas regras integradas que não é `final`. Isso significa que você pode estendê-lo e substituir o
Método `getRules()` para criar um conjunto reutilizável de regras:

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

E use-o como uma única regra regular:

```php
use Yiisoft\Validator\Validator;

$result = (new Validator())->validate('John', new UsernameRuleSet());
```

Também pode ser combinado com a regra [Nested] para reutilizar regras para vários atributos:

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

Até mesmo o problema de reutilizar apenas uma regra com os mesmos argumentos pode ser resolvido com `Composite`:

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

[opções de salto]: conditional-validation.md
[Nested]: built-in-rules-nested.md