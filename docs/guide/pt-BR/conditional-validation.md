# Validação condicional

As regras contêm diversas opções que podem ser ignoradas sob certas condições. Nem todas as regras suportam todas
essas opções, mas a grande maioria o faz.

## `skipOnError` - pula uma regra no conjunto se a anterior falhou

Por padrão, se ocorrer um erro durante a validação de um atributo, todas as regras adicionais do conjunto serão processadas. Para
Para alterar esse comportamento, use `skipOnError: true` para regras que precisam ser ignoradas:

No exemplo a seguir, a verificação do comprimento de um nome de usuário será ignorada se o nome de usuário não for preenchido.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'name' => [
        // Validated.
        new Required(),
        // Skipped because "name" is required but not filled.
        new Length(min: 4, max: 20, skipOnError: true),
        // Validated because "skipOnError" is "false" by default. Set to "true" to skip it as well.
        new Regex('^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$'),
    ],
    'age' => [
        // Validated because "age" is a different attribute with its own set of rules.
        new Required(),
        // Validated because "skipOnError" is "false" by default. Set to "true" to skip it as well.
        new Number(min: 21),
    ],
];
$result = (new Validator())->validate($data, $rules);
```

Observe que essa configuração deve ser definida para cada regra que precisa ser ignorada em caso de erro.

O mesmo efeito pode ser alcançado com as regras `StopOnError` e [`Composite`], que podem ser mais convenientes para um número maior
de regras.

Usando `StopOnError`:

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StopOnError;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'name' => new StopOnError([
        new Required(),
        new Length(min: 4, max: 20),
        new Regex('^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$'),
    ]),
];
$result = (new Validator())->validate($data, $rules);
```

Usando [`Composite`]:

```php
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'name' => [
        new Required(),
        new Composite(
            [
                new Length(min: 4, max: 20),
                new Regex('^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$'),
            ],
            skipOnError: true,
        )
    ],
];
$result = (new Validator())->validate($data, $rules);
```

## `skipOnEmpty` - ignorando uma regra se o valor validado estiver vazio

Por padrão, os valores ausentes/vazios dos atributos são validados. Se o valor estiver faltando, será considerado `null`.
Se você deseja que o atributo seja opcional, use `skipOnEmpty: true`.

Um exemplo com um atributo de idioma opcional:

```php
use Yiisoft\Validator\Rule\In;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'language' => [
        new In(['ru', 'en'], skipOnEmpty: true),
    ],
];
$result = (new Validator())->validate($data, $rules);
```

Se o atributo for obrigatório, é mais apropriado usar `skipOnError: true` junto com a regra `Required` anterior
em vez de `skipOnEmpty: true`. Isso ocorre porque a detecção de valores vazios dentro da regra `Required` e o salto
em outras regras podem ser definidas separadamente. Isso é descrito com mais detalhes abaixo,
consulte a seção [Configurando condição vazia em outras regras].

```php
use Yiisoft\Validator\Rule\In;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'language' => [
        new Required(),
        new In(['ru', 'en'], skipOnError: true),
    ],
];
$result = (new Validator())->validate($data, $rules);
```

### Noções básicas de condição vazia

O que é considerado vazio pode variar dependendo do escopo de uso.

O valor passado para [`skipOnEmpty`] é chamado de "condição vazia". Devido à normalização, os seguintes valores de atalho são
suportado:

- Quando `false` ou `null`, `Yiisoft\Validator\EmptyCondition\NeverEmpty` é usado automaticamente como retorno de chamada - cada valor
é considerado não vazio e validado sem pular (padrão).
- Quando `true`, `Yiisoft\Validator\EmptyCondition\WhenEmpty` é usado automaticamente como retorno de chamada - somente passado
(o atributo correspondente deve estar presente) e valores não vazios (não `null`, `[]` ou `''`) são validados.
- Se um callback personalizado for definido, ele será usado para determinar o vazio.

`false` geralmente é mais adequado para formulários HTML e `true` para APIs.

Existem mais algumas condições que não possuem atalhos e precisam ser definidas explicitamente porque são menos usadas:

- `Yiisoft\Validator\EmptyCondition\WhenMissing` - um valor é tratado como vazio apenas quando está faltando (não passou de jeito nenhum).
- `Yiisoft\Validator\EmptyCondition\WhenNull` - limita valores vazios apenas a `null`.

Um exemplo usando `WhenNull` como parâmetro (note que uma instância é passada, não um nome de classe):

```php
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\EmptyCondition\WhenNull;

new Integer(max: 100, skipOnEmpty: new WhenNull());
```

### Condição vazia personalizada

Para ainda mais personalização você pode usar sua própria classe que implementa o método mágico `__invoke()`. Aqui está um exemplo
onde um valor está vazio apenas se estiver faltando (ao usar atributos) ou for igual exatamente a zero.

```php
use Yiisoft\Validator\Rule\Number;

final class WhenZero
{
    public function __invoke(mixed $value, bool $isAttributeMissing): bool
    {
        return $isAttributeMissing || $value === 0;
    }
}

new Integer(max: 100, skipOnEmpty: new WhenZero());
```

Ou apenas um callable:

```php
use Yiisoft\Validator\Rule\Integer;

new Integer(
    max: 100,
    skipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {
        return $isAttributeMissing || $value === 0;
    }
);
```

Usar a classe tem o benefício da reutilização do código.

### Usando a mesma condição vazia não padrão para todas as regras

Para regras múltiplas, isso também pode ser definido de forma mais conveniente a nível do validator:

```php
use Yiisoft\Validator\RuleHandlerResolver\SimpleRuleHandlerContainer;
use Yiisoft\Validator\Validator;

$validator = new Validator(skipOnEmpty: true); // Using the shortcut.
$validator = new Validator(
    new SimpleRuleHandlerContainer(),
    // Using the custom callback.
    skipOnEmpty: static function (mixed $value, bool $isAttributeMissing): bool {
        return $value === 0;
    }
);
```

### Configurando condição vazia em outras regras

Algumas regras, como `Required`, não podem ser ignoradas por valores vazios - isso iria contra o propósito da regra.
No entanto, a condição vazia pode ser configurada aqui para detectar quando um valor está vazio. Observação: isso não ignora a regra.
Ele apenas determina qual é a condição vazia:

```php
use Yiisoft\Validator\Rule\Required;

$rule = new Required(
    emptyCondition: static function (mixed $value, bool $isAttributeMissing): bool {
        return $isAttributeMissing || $value === '';
    },
);
```

Também é possível configurá-lo globalmente para todas as regras deste tipo no nível do manipulador via
`RequiredHandler::$defaultEmptyCondition`.

## `when`

`when` fornece a opção de aplicar a regra dependendo de uma condição do callable fornecido. O resultado de uma chamada
determina se a regra será ignorada. A sintaxe da função é a seguinte:

```php
function (mixed $value, ValidationContext $context): bool;
```

onde:

- `$value` é um valor validado;
- `$context` é um contexto de validação;
- `true` como valor retornado significa que a regra deve ser aplicada e um `false` significa que ela deve ser ignorada.

Neste exemplo o estado só é obrigatório quando o país é `Brasil`. `$context->getDataSet()->getAttributeValue()`
O método permite que você obtenha o valor de qualquer outro atributo dentro do `ValidationContext`.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Validator;

$data = [];
$rules = [
    'country' => [
        new Required(),
        new Length(min: 2),
    ],
    'state' => new Required(
        when: static function (mixed $value, ValidationContext $context): bool {
            return $context->getDataSet()->getAttributeValue('country') === 'Brasil';
        },
    )
];
$result = (new Validator())->validate($data, $rules);
```

Como alternativa às funções, classes que podem ser chamadas podem ser usadas. Essa abordagem tem a vantagem da capacidade de reutilização do código.
Consulte a seção [Pular quando vazio] para ver um exemplo.

[Configurando condição vazia em outras regras]: #configurando-condição-vazia-em-outras-regras
[Pular quando vazio]: #skiponempty---ignorando-uma-regra-se-o-valor-validado-estiver-vazio
[`Composite`]: built-in-rules-composite.md
[`skipOnEmpty`]: conditional-validation.md#skiponempty---ignorando-uma-regra-se-o-valor-validado-estiver-vazio