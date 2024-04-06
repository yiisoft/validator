# Resultados

O resultado da validação é um objeto que contém os erros ocorridos durante a validação.

## A validação foi bem-sucedida?

Para apenas verificar o status da validação (se um dado é válido como um todo), use a seguinte chamada de API `Result`:

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->isValid();
```

Pode ser reduzido a um atributo específico:

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->isAttributeValid('name');
```

## Erros

Na maioria das vezes, informar apenas o status da validação não é suficiente. Existem vários métodos para obter erros detalhados
lista com seus dados do resultado. A diferença entre eles está no agrupamento, filtragem e representação de cada
erro. Escolha um que atenda às suas necessidades, dependendo da situação.

### Lista simples de mensagens de erro

Um dos casos mais simples é obter uma lista simples de todas as mensagens de erro. Use a seguinte chamada de API `Result`:


```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getErrorMessages();
```

Um exemplo de saída com atributos `age` e `email`:

```php
[
     'O valor não deve ser inferior a 21.',
     'Este valor não é um endereço de e-mail válido.',
     'Uma mensagem de erro personalizada.',
];
```

É fácil de exibir e iterar, porém, com uma quantidade maior de atributos e dependendo da mensagem, pode ser
problemático entender a qual atributo um erro pertence.

#### Mensagens de erro não vinculadas a um atributo específico

Às vezes, as mensagens de erro não estão relacionadas a um atributo específico. Isso pode acontecer durante a validação de
vários atributos dependendo uns dos outros, por exemplo. Use a seguinte chamada de API `Result`:

```php
$result->getCommonErrorMessages();
```

A saída, por exemplo, acima:

```php
[
     'Uma mensagem de erro personalizada.',
];
```

#### Filtrando por um atributo específico

Esta lista também pode ser filtrada por um atributo específico. Somente atributos de nível superior são suportados.

```php
$result->getAttributeErrorMessages('email');
```

A saída, por exemplo, acima:

```php
[
     'Este valor não é um endereço de e-mail válido.',
];
```

### Mensagens de erro indexadas por atributo

Para agrupar mensagens de erro por atributo, use a seguinte chamada de API `Result`:

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getErrorMessagesIndexedByAttribute();
```

Um exemplo de saída:

```php
[
     'name' => [
         'O valor não pode ficar em branco.',
         'Este valor deve conter pelo menos 4 caracteres.',
     ],
     'email' => ['Este valor não é um endereço de e-mail válido.'],
     // Mensagens de erro não vinculadas a um atributo específico são agrupadas em uma chave de string vazia.
     '' => ['Uma mensagem de erro personalizada.'],
];
```

Observe que o resultado é sempre uma matriz bidimensional com nomes de atributos como chaves no primeiro nível de aninhamento. Isso significa
que o aninhamento adicional de atributos não é suportado (mas pode ser alcançado
usando [`getErrorMessagesIndexedByPath()`](#Mensagens-de-erro-indexadas-por-caminho)).
Voltando ao exemplo anterior, quando `name` e `email` pertencem a um atributo `user`, a saída será:

```php
[
     'user' => [
         'O valor não pode ficar em branco.',
         'Este valor deve conter pelo menos 4 caracteres.',
         'Este valor não é um endereço de e-mail válido.'
     ],
     // Mensagens de erro não vinculadas a um atributo específico são agrupadas em uma chave de string vazia.
     '' => ['Uma mensagem de erro personalizada.'],
];
```

Lembre-se também de que os nomes dos atributos devem ser strings, mesmo quando usados com `Each`:

```php
$rule = new Each([new Number(min: 21)]),
```

Com a entrada contendo chaves sem string para atributos de nível superior, por exemplo, `[21, 22, 23, 20]`, InvalidArgumentException` será lançada.

Mesmo o array `['1' => 21, '2' => 22, '3' => 23, '4' => 20]` causará um erro, porque o PHP [converterá chaves para o tipo int].

Mas se for fornecido um array com chaves de string `['1a' => 21, '2b' => 22, '3c' => 23, '4d' => 20]`, a saída será:

```php
[
     '4d' => [
         0 => 'O valor não deve ser inferior a 21.'
     ]
]
```

### Mensagens de erro indexadas por caminho

Esta é provavelmente a representação mais avançada oferecida pelos métodos integrados. O agrupamento é feito por caminho - um
sequência de atributos concatenados mostrando a localização do valor com erro em uma estrutura de dados. Um separador é personalizável,
a notação de ponto é definida como padrão. Use a seguinte chamada de API `Result`:

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getErrorMessagesIndexedByPath();
```

Um exemplo de saída:

```php
[
     'user.firstName' => ['O valor não pode ficar em branco.'],
     'user.lastName' => ['Este valor deve conter pelo menos 4 caracteres.'],
     'email' => ['Este valor não é um endereço de e-mail válido.'],
     // Mensagens de erro não vinculadas a um atributo específico são agrupadas em uma chave de string vazia.
     '' => ['Uma mensagem de erro personalizada.'],
];
```

Um caminho também pode conter elementos inteiros (ao usar a regra `Each`, por exemplo):

```php
[
     'charts.0.points.0.coordinates.y' => ['O valor não deve ser maior que 10.'],
];
```

#### Resolvendo colisão de caracteres especiais em nomes de atributos

Quando o atributoO nome do ibute na lista de mensagens de erro contém um separador de caminho (ponto `.` por padrão),
ele é escapado automaticamente usando uma barra invertida (`\`):

```php
[
     'country\.code' => ['O valor não pode ficar em branco.'],
],
```

No caso de usar um único atributo por conjunto de regras, quaisquer modificações adicionais nos nomes dos atributos nas regras
configuração não é necessária, então eles devem permanecer como estão:

```php
use Yiisoft\Validator\Rule\In;
use Yiisoft\Validator\Rule\Required;

$rules = [
    'country.code' => [
        new Required();
        new In(['ru', 'en'], skipOnError: true),
    ],
];
```

No entanto, ao usar a regra `Nested` com vários atributos por conjunto de regras, os caracteres especiais precisam ser escapados com
uma barra invertida (`\`) para que os caminhos dos valores estejam corretos e seja possível revertê-los de string para individual
Unid. Consulte a seção [Usando teclas contendo separador/atalho] para obter mais detalhes.

Isso pode ser usado como uma alternativa ao uso de um separador personalizado.

#### Filtrando por um atributo específico

Esta lista também pode ser filtrada por um atributo específico. Somente atributos de nível superior são suportados.

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getAttributeErrorMessagesIndexedByPath('user');
```

A saída, por exemplo, acima:

```php
[
     'firstName' => ['O valor não pode ficar em branco.'],
     'lastName' => ['Este valor deve conter pelo menos 4 caracteres.'],
];
```

## Lista de objetos de erro

Quando mesmo essas representações não são suficientes, uma lista inicial não modificada de objetos de erro pode ser acessada via
este método:

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getErrors();
```

Cada erro armazena os seguintes dados:

- Mensagem. Uma mensagem simples como "Este valor está errado." ou um modelo com espaços reservados entre chaves
   (`{}`), por exemplo: `O valor não deve ser inferior a {min}.`. A formatação real é feita no `Validator` dependendo
   o tradutor configurado.
- Parâmetros do modelo para substituição durante a formatação, por exemplo: `['min' => 7]`.
- Um caminho para um valor dentro de uma estrutura de dados verificada, por exemplo: `['user', 'name', 'firstName']`.

### Um exemplo de aplicativo

Para que isso pode ser útil? Por exemplo, para construir uma árvore aninhada de mensagens de erro indexadas por nomes de atributos:

```php
[
     'user' => [
         'name' => [
             'firstName' => ['O valor não pode ficar em branco.'],
             'lastName' => ['Este valor deve conter pelo menos 4 caracteres.'],
         ],
     ],
     'email' => ['Este valor não é um endereço de e-mail válido.'],
];
```

Ele não é fornecido intencionalmente imediatamente devido à complexidade da iteração. No entanto, isto pode ser útil para despejar
como JSON e armazenamento em logs, por exemplo.

A depuração de objetos de erro originais também é mais conveniente.

### Filtrando por um atributo específico

Esta lista também pode ser filtrada por um atributo específico. Somente atributos de nível superior são suportados.

```php
use Yiisoft\Validator\Result;

/** @var Result */
$result->getAttributeErrors('email');
```

[Usando chaves contendo separador/atalho]: built-in-rules-nested.md#usando-chaves-contendo-separador--atalho
[converterá chaves para o tipo int]: https://www.php.net/manual/en/language.types.array.php