# `Nested` - validação de dados aninhados e relacionados

## Uso básico (relação um para um)

Em muitos casos, há necessidade de validar dados relacionados além da entidade/modelo atual.
Existe uma regra `Nested` ("Aninhamento") para esta finalidade.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

$data = ['author' => ['name' => 'John', 'age' => '17']];
$rule = new Nested([
    'title' => [new Required()],
    'author' => new Nested([
        'name' => [new Length(min: 3)],
        'age' => [new Number(min: 18)],
    ]),
]);
$errors = (new Validator())->validate($data, $rule)
->getErrorMessagesIndexedByPath();
```

A saída de `$errors` será:

```php
[
     'title' => ['O valor não pode ficar em branco.'],
     'author.age' => ['O valor não deve ser inferior a 18.'],
];
```
Neste exemplo, uma instância adicional da regra `Nested` é usada para cada relação. Outras formas de configuração
são possíveis e são descritos abaixo.

Outras representações da lista de erros são abordadas na seção [Resultados].

## Opções de configuração

### Notação de ponto

A notação de ponto pode ser usada como um método alternativo de configuração. Neste caso, o exemplo acima é representado como
segue:

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

$rule = new Nested([
    'title' => [new Required()],
    'author.name' => [new Length(min: 3)],
    'author.age' => [new Number(min: 18)],
]);
```

Também é possível combinar as duas abordagens:

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

$data = ['author' => ['name' => 'Alexey', 'age' => '31']];
$rule = new Nested([
    'content' => new Nested([
        'title' => [new Required()],
        'description' => [new Required()],
    ]),
    'author.name' => [new Length(min: 3)],
    'author.age' => [new Number(min: 18)],
]);
```

### Omitindo código

Algum código pode ser omitido por questões de brevidade.

#### Instâncias `nested` internas

Instâncias `nested` internas podem ser omitidas:

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;

$rule = new Nested([
    'author' => [
        'name' => [new Length(min: 1)],
    ],
]);
```

#### Arrays internas para regras únicas

Arrays internas para regras únicas podem ser omitidas independentemente do nível de aninhamento:

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;

$rule = new Nested([
    'author' => [
        'name' => [
            'surname' => new Length(min: 1),
        ],
    ],
]);
```

## Uso avançado

### Relações um-para-muitos e muitos-para-muitos

O exemplo na seção [Uso básico] mostra como trabalhar apenas com relações um-para-um, onde a regra `Nested` é
suficiente para referenciar as relações. Pode ser combinado com outras regras complexas, como `Each`, para validar
relações um-para-muitos e muitos-para-muitos também:

Vamos pegar este conjunto de gráfico de linhas como exemplo:

```php
$data = [
    'charts' => [
        [
            'points' => [
                ['coordinates' => ['x' => -11, 'y' => 11], 'rgb' => [1, 255, 0]],
                ['coordinates' => ['x' => -12, 'y' => 12], 'rgb' => [0, 2, 255]],
            ],
        ],
        [
            'points' => [
                ['coordinates' => ['x' => -1, 'y' => 1], 'rgb' => [0, 0, 0]],
                ['coordinates' => ['x' => -2, 'y' => 2], 'rgb' => [128, 128, 128]],
            ],
        ],
        [
            'points' => [
                ['coordinates' => ['x' => -13, 'y' => 13], 'rgb' => [3, 255, 0]],
                ['coordinates' => ['x' => -14, 'y' => 14], 'rgb' => [0, 4, 255]],
            ],
        ],
    ],
];
```

A representação visual pode ser assim (também disponível em [JSFiddle]):

![Exemplo de gráfico](../images/chart.png)

Vamos adicionar as regras e modificar um pouco os dados para conter itens inválidos:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Validator;

$data = [
    'charts' => [
        [
            'points' => [
                ['coordinates' => ['x' => -11, 'y' => 11], 'rgb' => [-1, 256, 0]],
                ['coordinates' => ['x' => -12, 'y' => 12], 'rgb' => [0, -2, 257]],
            ],
        ],
        [
            'points' => [
                ['coordinates' => ['x' => -1, 'y' => 1], 'rgb' => [0, 0, 0]],
                ['coordinates' => ['x' => -2, 'y' => 2], 'rgb' => [255, 255, 255]],
            ],
        ],
        [
            'points' => [
                ['coordinates' => ['x' => -13, 'y' => 13], 'rgb' => [-3, 258, 0]],
                ['coordinates' => ['x' => -14, 'y' => 14], 'rgb' => [0, -4, 259]],
            ],
        ],
    ],
];
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
$result = (new Validator())->validate($data, $rule);
$errors = $result->getErrorMessagesIndexedByPath();
```

O conteúdo dos erros será:

```php
$ erros = [
     'charts.0.points.0.coordinates.x' => ['O valor não deve ser inferior a -10.'],
     // ...
     'charts.0.points.0.rgb.0' => ['O valor não deve ser inferior a 0. -1 fornecido.'],
     // ...
];
```

### Usando o atalho `*`

Um atalho `*` pode ser usado para simplificar as combinações `Nested` e `Each`:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;

$rule = new Nested([
    'charts.*.points.*.coordinates.x' => [new Number(min: -10, max: 10)],
    'charts.*.points.*.coordinates.y' => [new Number(min: -10, max: 10)],
    'charts.*.points.*.rgb' => [
        new Count(3),
        new Number(min: 0, max: 255),
    ],
]);
```

Com agrupamento adicional, também pode ser reescrito assim:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;

$rule = new Nested([
    'charts.*.points.*.coordinates' => new Nested([
        'x' => [new Number(min: -10, max: 10)],
        'y' => [new Number(min: -10, max: 10)],
    ]),
    'charts.*.points.*.rgb' => [
        new Count(3),
        new Number(min: 0, max: 255),
    ],
]);
```

Isso é menos detalhado, mas a desvantagem dessa abordagem é que você não pode configurar adicionalmente
pares `Nested` e `Each`. Se você precisar fazer isso, use a forma explícita de configuração (veja o exemplo fornecido na
seção [Uso básico]).

### Usando atributos PHP

Regras e relações podem ser declaradas via DTO com atributos PHP:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;

final class ChartSet
{
    public function __construct(
        #[Each([new Nested(Chart::class)])]
        private array $charts,
    ) {
    }
}

final class Chart
{
    public function __construct(
        #[Each([new Nested(Point::class)])]
        private array $points,
    ) {
    }
}

final class Point
{
    public function __construct(
        #[Nested(Coordinates::class)]
        private Coordinates $coordinates,
        #[Count(exactly: 3)]
        #[Each([new Number(min: 0, max: 255)])]
        private array $rgb,
    ) {
    }
}

final class Coordinates
{
    public function __construct(
        #[Number(min: -10, max: 10)]
        private int $x,
        #[Number(min: -10, max: 10)]
        private int $y,
    ) {
    }
}
```

Com os dados do array associativo dos exemplos anteriores, podemos usar a classe apenas para buscar as regras:

```php
$data = [
    'charts' => [
        [
            'points' => [
                ['coordinates' => ['x' => -11, 'y' => 11], 'rgb' => [-1, 256, 0]],
                ['coordinates' => ['x' => -12, 'y' => 12], 'rgb' => [0, -2, 257]],
            ],
        ],
        [
            'points' => [
                ['coordinates' => ['x' => -1, 'y' => 1], 'rgb' => [0, 0, 0]],
                ['coordinates' => ['x' => -2, 'y' => 2], 'rgb' => [255, 255, 255]],
            ],
        ],
        [
            'points' => [
                ['coordinates' => ['x' => -13, 'y' => 13], 'rgb' => [-3, 258, 0]],
                ['coordinates' => ['x' => -14, 'y' => 14], 'rgb' => [0, -4, 259]],
            ],
        ],
    ],
];
$result = $validator->validate($data, ChartSet::class);
```

Ou forneça dados junto com regras nos mesmos objetos:

```php
$chartSet = new ChartSet(
    charts: [
        new Chart(
            points: [
                new Point(
                    coordinates: new Coordinates(x: -11, y: 11),
                    rgb: [-1, 256, 0],
                ),
                new Point(
                    coordinates: new Coordinates(x: -12, y: 12),
                    rgb: [0, -2, 257],
                ),
            ],       
        ),
        new Chart(
            points: [
                new Point(
                    coordinates: new Coordinates(x: -1, y: 1),
                    rgb: [0, 0, 0],
                ),
                new Point(
                    coordinates: new Coordinates(x: -2, y: 2),
                    rgb: [255, 255, 255],
                ),
            ],       
        ),
        new Chart(
            points: [
                new Point(
                    coordinates: new Coordinates(x: -13, y: 13),
                    rgb: [-3, 258, 0],
                ),
                new Point(
                    coordinates: new Coordinates(x: -14, y: 14),
                    rgb: [0, -4, 259],
                ),
            ],       
        ),
    ],
);
$result = $validator->validate($chartSet); // Note `$rules` argument is `null` here.
$errors = $result->getErrorMessagesIndexedByPath();
```

- Para mais informações sobre as diferentes formas de configurar regras via atributos PHP, veja 
[Configurando regras via atributos PHP].
- Para mais informações sobre possíveis combinações de dados/regras passadas para validação, consulte a seção [Usando validator].

### Usando chaves contendo separador/atalho

Se uma chave contém o separador (`.`) ou o atalho `Each` (`*`), ela deve ser escapada com uma barra invertida (`\`) para que
a configuração da regra funcione corretamente. Nos dados de entrada, o escape não é necessário. Aqui está um exemplo com 2 chaves
aninhadas denominadas `author.data` e `name.surname`:

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;

$rule = new Nested([
    'author\.data.name\.surname' => [
        new Length(min: 3),
    ],
]);
$data = [
    'author.data' => [
        'name.surname' => 'Dmitry',
    ],
];

Observe que o escape ainda é necessário ao usar diversas regras aninhadas para configuração:

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;

$rule = new Nested([
    'author\.data' => new Nested([
        'name\.surname' => [
            new Length(min: 3),
        ],
    ]),
]);
$data = [
    'author.data' => [
        'name.surname' => 'Dmitriy',
    ],
];
```

O exemplo com o atalho `Each` (`*`):

```php
use Yiisoft\Validator\Rule\Nested;

$rule = new Nested([
    'charts\.list.*.points\*list.*.coordinates\.data.x' => [
        // ...
    ],
    'charts\.list.*.points\*list.*.coordinates\.data.y' => [
        // ...
    ],
    'charts\.list.*.points\*list.*.rgb' => [
        // ...
    ],
]);
$data = [
    'charts.list' => [
        [
            'points*list' => [
                [
                    'coordinates.data' => ['x' => -11, 'y' => 11], 'rgb' => [-1, 256, 0],
                ],
            ],
        ],
    ],
];
```

[Resultados]: result.md
[Uso básico]: #uso-básico-relação-um-para-um
[JSFiddle]: https://jsfiddle.net/fys8uadr/
[Configurando regras via atributos PHP]: configuring-rules-via-php-attributes.md
[Usando validator]: using-validator.md