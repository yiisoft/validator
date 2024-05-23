# Validação do lado do cliente

Ao contrário do Yii2, o pacote não fornece nenhum tratamento das regras de validação no lado do cliente. Provavelmente será
adicionado posteriormente como outro pacote relacionado.

Porém, existe a possibilidade de exportar opções de regras como um array para passar para o lado do cliente usando
a classe `RulesDumper`:

- Múltiplas regras e aninhamento de regras são suportadas.
- Se uma regra não fornecer opções, apenas o nome será exportado.
- Os valores de opção que não podem ser serializados/reproduzidos no lado do cliente - [`callables`], por exemplo, são excluídos - outros
 completamente, como `Callback::$callback`, ou parcialmente como `$skipOnEmpty` se vários tipos forem suportados.

Dada a regra `Length` integrada:

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

A saída será:
```php
[  
    'name' => [  
        [  
            'length',  
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

A matriz resultante, serializada como JSON, pode ser desserializada novamente e aplicada a uma implementação de sua escolha.

## Estrutura das opções exportadas

Aqui estão algumas especificações da estrutura de regras:

- É mantida a indexação das regras por nomes de atributos.
- O primeiro elemento de regra é sempre um nome de regra com um índice inteiro de `0`.
- Os restantes elementos da regra são pares chave-valor, onde chave é um nome de opção e valor é um valor de opção correspondente.
- Para regras complexas, como [`Composite`], [`Each`] e [`Nested`], as opções das regras filhas estão localizadas na chave `rules`.

Observe que as mensagens de erro possuem uma estrutura especial:

```php
[
    'lessThanMinMessage' => [  
        'template' => 'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.',  
        'parameters' => ['min' => 4],  
    ],
];
```

Permanece o mesmo independentemente da presença de espaços reservados e parâmetros:

```php
'message' => [
    'template' => 'Value is invalid.',
    'parameters' => [],
],
```

[`callables`]: https://www.php.net/manual/pt_BR/language.types.callable.php
[`Nested`]: built-in-rules-nested.md
[`Each`]: built-in-rules-each.md
[`Composite`]: built-in-rules-composite.md
