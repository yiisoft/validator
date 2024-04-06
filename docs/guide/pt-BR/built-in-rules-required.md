# `Required` - verificando se um valor obrigatório é fornecido

Use a regra `Required` para garantir que um valor seja fornecido (não vazio).

Por padrão, um valor é considerado vazio somente quando é:

- Não passou de jeito nenhum.
- `null`.
- Uma string vazia (após o trimming).
- Um iterável vazio.

## Personalizando condição vazia

Quais valores são considerados vazios podem ser personalizados através da opção `$emptyCondition`. Ao contrário de [skipOnEmpty],
nenhuma normalização é realizada aqui, portanto, apenas uma classe callback ou especial é aceita. Para mais detalhes veja
seção [Fundamentos básicos da condição de vazio].

Um exemplo com condição vazia personalizada que limita valores vazios apenas a `null`:

```php
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\EmptyCondition\WhenNull;

new Required(emptyCondition: new WhenNull());
```

Também é possível configurá-lo globalmente para todas as regras deste tipo no nível do manipulador via
`RequiredHandler::$defaultEmptyCondition`.

## Uso com outras regras

`Required` raramente é usado sozinho. Ao combiná-lo com outras regras, certifique-se de que seja colocado em primeiro lugar:

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

$rules = [
    new Required(),
    new Length(min: 1, max: 50),
];
```

Com essas configurações, `Length` ainda será executado no caso de um valor vazio. Para evitar isso, configure um condicional
validação:

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

$rules = [
    new Required(),
    new Length(min: 1, max: 50, skipOnError: true),
];
```

Outras formas de configurar a validação condicional são descritas na seção [Validação condicional].

[skipOnEmpty]: conditional-validation.md#skiponempty---ignorando-uma-regra-se-o-valor-validado-estiver-vazio
[Fundamentos básicos da condição de vazio]: conditional-validation.md#Noções-básicas-de-condição-vazia
[Validação condicional]: conditional-validation.md