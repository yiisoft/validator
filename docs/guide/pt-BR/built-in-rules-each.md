# `Each` - aplicando as mesmas regras para cada item de dados do conjunto

A regra `Each` permite que as mesmas regras sejam aplicadas a cada item de dados do conjunto. O exemplo a seguir mostra
a configuração para validação de componentes [cor RGB]:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;

new Each([
    new Integer(min: 0, max: 255),
]);
```

Combinando com outra regra integrada chamada `Count` podemos ter certeza de que o número de componentes é exatamente 3:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;

$rules = [
    // Applies to a whole set.
    new Count(3),
    // Applies to individual set items.
    new Each(        
        // For single rules, wrapping with array / iterable is not necessary.
        new Integer(min: 0, max: 255),
    ),
];
```

Os itens de dados validados não estão limitados apenas a valores "simples" - `Each` pode ser usado dentro de um `Nested` e conter regras `Nested` englobando relações um para muitos e muitos para muitos:

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

Para obter mais informações sobre como usá-lo com `Nested`, consulte o guia [Nested].

[cor RGB]: https://en.wikipedia.org/wiki/RGB_color_model
[Nested]: built-in-rules-nested.md
