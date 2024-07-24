# Criando regras personalizadas

Quando a lógica de validação desejada está faltando nas regras e extensões integradas, é hora de criar uma regra personalizada.

## Conceito de regras

A principal característica do conceito de regras é a separação em 2 partes:

- Rule (uma classe que implementa `RuleInterface`). Ele armazena apenas opções de configuração e uma referência ao seu manipulador. Ele
não realiza a validação real.
- Rule handler - Manipulador de regras (uma classe que implementa `RuleHandlerInterface`). Dada uma regra e dados de entrada, executa o procedimento real
da validação no contexto da validação atual.

Além da separação de responsabilidades, esta abordagem permite resolver automaticamente dependências de um manipulador. 
Por exemplo, se você precisar de um objeto de conexão de banco de dados dentro de um manipulador, não será necessário passá-lo explicitamente - ele
pode ser obtido automaticamente de um contêiner de dependência.

## Instruções para criar uma regra personalizada e o que evitar

Vamos tentar criar uma regra para verificar se um valor é uma [cor RGB] válida.

### Criando uma regra

O primeiro passo é criar uma regra:

```php
use Yiisoft\Validator\RuleInterface;

final class RgbColor implements RuleInterface 
{  
    public function __construct(
        public readonly string $message = 'Invalid RGB color value.',  
    ) {  
    }  
  
    public function getName(): string  
    {  
        return 'rgbColor';  
    }  
  
    public function getHandler(): string  
    {  
        return RgbColorHandler::class;  
    }  
}
```

> **Nota:** [Propriedades somente leitura] são suportadas apenas a partir do PHP 8.1.

Além das implementações de métodos de interface necessárias, ele contém apenas mensagens de erro personalizáveis. Claro, mais recursos
podem ser adicionados - validação condicional, opções do cliente, etc. Mas isso é o mínimo para começar.

### Criando um manipulador

A segunda etapa é criar o manipulador. Vamos definir o que é exatamente uma [cor RGB] válida:

- É um array (lista para ser exato).
- Contém exatamente 3 itens.
- Cada item é um número inteiro no intervalo de 0 a 255.

```php
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class RgbColorHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        // Every rule handler must start with this check.  
        if (!$rule instanceof RgbColor) {
            throw new UnexpectedRuleException(RgbColor::class, $rule);
        }

        if (!is_array($value) || array_keys($value) !== [0, 1, 2]) {
            return (new Result())->addError($rule->getMessage());
        }

        foreach ($value as $item) {
            if (!is_int($item) || $item < 0 || $item > 255) {
                return (new Result())->addError($rule->getMessage());
            }
        }

        return new Result();
    }
}
```

> **Nota:** Um método `validate()` não se destina a ser chamado diretamente. Resolver o manipulador e chamar o método
> ocorre automaticamente ao usar o `Validator`.

### Dicas para melhorar o código

#### Mensagens de erro mais específicas

Prefira mensagens de erro mais específicas às gerais. Mesmo que isso exija uma quantidade maior de mensagens e códigos, ajuda a
entender mais rapidamente o que exatamente há de errado com os dados de entrada. A [cor RGB] é uma estrutura bastante simples e compacta, mas no caso
de dados mais complexos, certamente valerá a pena.

Tendo isso em mente, a regra pode ser reescrita mais ou menos assim:

```php
use Yiisoft\Validator\RuleInterface;

final class RgbColor implements RuleInterface 
{  
    public function __construct(
        public readonly string $incorrectInputTypeMessage = 'Value must be an array. {type} given.',
        public readonly string $incorrectInputRepresentationMessage = 'Value must be a list.',
        public readonly string $incorrectItemsCountMessage = 'Value must contain exactly 3 items. ' . 
        '{itemsCount} {itemsCount, plural, one{item} other{items}} given.',
        public readonly string $incorrectItemTypeMessage = 'Every item must be an integer. {type} given at ' .
        '{position, selectordinal, one {#st} two {#nd} few {#rd} other {#th}} position.',          
        public readonly string $incorrectItemValueMessage = 'Every item must be between 0 and 255. {value} given at ' . 
        '{position, selectordinal, one {#st} two {#nd} few {#rd} other {#th}} position.',          
    ) {  
    }
  
    public function getName(): string  
    {  
        return 'rgbColor';  
    }  
  
    public function getHandler(): string  
    {  
        return RgbColorHandler::class;  
    }  
}
```

> **Notas:**
>- [Propriedades somente leitura] são suportadas apenas a partir do PHP 8.1.
>- A formatação usada em `$incorrectItemTypeMessage` e `$incorrectItemValueMessage` requer a extensão [PHP `intl`].

O manipulador precisa ser alterado de acordo. Vamos também adicionar parâmetros de erro para poder usá-los como espaços reservados em
modelos de mensagens:

```php
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class RgbColorHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof RgbColor) {
            throw new UnexpectedRuleException(RgbColor::class, $rule);
        }

        if (!is_array($value)) {
            return (new Result())->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedProperty(),
                'type' => get_debug_type($value),
            ]);
        }

        $itemsCount = 0;
        foreach (array_keys($value) as $index => $keyValue) {
            if ($keyValue !== $index) {
                return (new Result())->addError($rule->getIncorrectInputRepresentationMessage(), [
                    'attribute' => $context->getTranslatedProperty(),
                ]);
            }

            $itemsCount++;
        }

        if ($itemsCount !== 3) {
            return (new Result())->addError($rule->getIncorrectItemsCountMessage(), [
                'attribute' => $context->getTranslatedProperty(),
                'itemsCount' => $itemsCount,
            ]);
        }

        foreach ($value as $index => $item) {
            if (!is_int($item)) {
                return (new Result())->addError($rule->getIncorrectItemTypeMessage(), [
                    'attribute' => $context->getTranslatedProperty(),
                    'position' => $index + 1,
                    'type' => get_debug_type($item),
                ]);
            }

            if ($item < 0 || $item > 255) {
                return (new Result())->addError($rule->getIncorrectItemValueMessage(), [
                    'attribute' => $context->getTranslatedProperty(),
                    'position' => $index + 1,
                    'value' => $value,
                ]);
            }
        }

        return new Result();
    }
}
```

> **Nota:** Também é uma boa ideia utilizar os recursos da versão do idioma usado. Por exemplo, para PHP >= 8.1 podemos
> simplificar a verificação de que um determinado array é uma lista com a função [array_is_list()].

#### Usando regras integradas, se possível

Antes de criar uma regra personalizada, verifique se ela pode ser substituída por uma regra integrada ou um conjunto de regras. Se sim, é
desnecessário criar uma regra personalizada.

##### Substituindo por `Composite` ("Agrupamento")

O exemplo com [cores RGB] pode ser significativamente simplificado depois de perceber que também é possível obter o mesmo
efeito usando apenas regras internas:

```php
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;

$rules = [
    new Count(3),
    new Each([new Integer(min: 0, max: 255)])
];
```

Torná-los reutilizáveis não é muito mais difícil - todo o conjunto pode ser colocado dentro de uma regra [`Composite`] e usado como uma única
regra regular.

```php
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Validator;

final class RgbColorRuleSet extends Composite
{
    public function getRules(): array
    {
        return [
            new Count(3),
            new Each([new Integer(min: 0, max: 255)])
        ];
    }
}

$result = (new Validator())->validate([205, 92, 92], new RgbColorRuleSet());
```

##### Substituindo por regras separadas e [`when`]

Abaixo está uma tentativa de usar o contexto de validação para validar atributos dependendo uns dos outros:

- Valide o nome da empresa somente quando o outro atributo `hasCompany` estiver preenchido.
- O nome da empresa deve ser uma string com comprimento entre 1 e 50 caracteres.

```php
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class CompanyNameHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof CompanyName) {
            throw new UnexpectedRuleException(CompanyName::class, $rule);
        }

        if ($context->getDataSet()->getAttributeValue('hasCompany') !== true) {
            return new Result();
        }

        if (!is_string($value)) {
            return (new Result())->addError($rule->getIncorrectInputMessage());
        }

        $length = strlen($value);
        if ($length < 1 || $length > 50) {
            return (new Result())->addError($rule->getMessage());
        }

        return new Result();
    }
}
```

Esta regra personalizada também pode ser separada e refatorada usando regras integradas que reduzem o agrupamento:

```php
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\ValidationContext;

$rules = [
    'hasCompany' => new BooleanValue(),
    'companyName' => new Length(
        min: 1,
        max: 50,
        when: static function (mixed $value, ValidationContext $context): bool {
            return $context->getDataSet()->getAttributeValue('hasCompany') === true;
        },
    ),
];
```

## Mais exemplos

A ideia dos exemplos anteriores era mostrar o processo de criação de regras customizadas com manipuladores usando o princípio 
"aprender por erros". Portanto, em termos de uso prático, eles provavelmente são menos valiosos devido à substituição por regras integradas.
Conhecendo os princípios básicos, vamos explorar exemplos mais apropriados da vida real.

### Verificando `YAML`

Existe uma regra integrada para validar JSON. Mas e se precisarmos da mesma coisa, mas para [YAML]? Vamos tentar
implementá-lo.

Regra:

```php
use Yiisoft\Validator\RuleInterface;

final class Yaml implements RuleInterface 
{  
    public function __construct(
        public readonly string $incorrectInputMessage = 'Value must be a string. {type} given.',        
        public readonly string $message = 'The value is not a valid YAML.',          
    ) {  
    }
  
    public function getName(): string  
    {  
        return 'yaml';  
    }  
  
    public function getHandler(): string  
    {  
        return YamlHandler::class;  
    }  
}
```

> **Nota:** [Propriedades somente leitura] são suportadas apenas a partir do PHP 8.1.

Manipulador:

```php
use Exception;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class YamlHandler implements RuleHandlerInterface
{  
    public function validate(mixed $value, object $rule, ValidationContext $context): Result 
    {  
        if (!$rule instanceof Yaml) {
            throw new UnexpectedRuleException(RgbColor::class, $rule);
        }
  
        if (!is_string($value)) {
            return (new Result())->addError($rule->getMessage(), [
                'attribute' => $context->getTranslatedProperty(),
                'type' => get_debug_type($value),
            ]);
        }

        try {
            $data = yaml_parse($value);
        } catch (Exception $e) {
            return (new Result())->addError($rule->getMessage(), [
                'attribute' => $context->getTranslatedProperty(),
            ]);
        }

        if ($data === false) {
            return (new Result())->addError($rule->getMessage(), [
                'attribute' => $context->getTranslatedProperty(),
            ]);
        }

        return new Result();  
    }
}
```

> **Notas:**
>- O uso de [`yaml_parse()`] requer adicionalmente a extensão [PHP `yaml`].
>- Processar entradas de usuários não confiáveis com [`yaml_parse()`] pode ser perigoso com certas configurações.
>Consulte a documentação para mais detalhes.

### Wrapping validation

Um dos usos corretos do contexto de validação pode envolver a validação com alguma lógica adicional. Isso pode ser usado
para implementar [cenários do Yii 2], por exemplo.

```php
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class OnHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof On) {
            throw new UnexpectedRuleException(On::class, $rule);
        }

        $scenario = $context->getParameter(On::SCENARIO_PARAMETER);

        try {
            $scenario = $this->prepareScenarioValue($scenario);
        } catch (InvalidArgumentException) {
            return (new Result())
                ->addError(
                    sprintf(
                        'Scenario must be null, a string or "\Stringable" type, "%s" given.',
                        get_debug_type($scenario),
                    ),
                );
        }

        return $this->isSatisfied($rule, $scenario)
            // With active scenario, perform the validation.
            ? $context->validate($value, $rule->getRules())
            // With all other scenarios, skip the validation.
            : new Result();
    }
}
```

Este trecho de código foi retirado da extensão [Yii Validator Scenarios]. Leia mais na seção [Cenários].

## Criando uma extensão

Com uma regra personalizada, você pode ir ainda mais longe. Se não for muito específico do projeto e você achar que pode ser útil
para outra pessoa, disponibilize-o como uma [`extensão`].

[Cenários]: extensions.md#cenários
[Yii Validator Scenarios]: https://github.com/vjik/yii-validator-scenarios
[cor RGB]: https://en.wikipedia.org/wiki/RGB_color_model
[Propriedades somente leitura]: https://www.php.net/manual/pt_BR/language.oop5.properties.php#language.oop5.properties.readonly-properties
[PHP `intl`]: https://www.php.net/manual/pt_BR/book.intl.php
[array_is_list()]: https://www.php.net/manual/pt_BR/function.array-is-list.php
[`Composite`]: built-in-rules-composite.md
[YAML]: https://pt.wikipedia.org/wiki/YAML
[`yaml_parse()`]: https://www.php.net/manual/pt_BR/function.yaml-parse.php
[PHP `yaml`]: https://www.php.net/manual/pt_BR/book.yaml.php
[cenários do Yii 2]: https://www.yiiframework.com/doc/guide/2.0/en/structure-models#scenarios
[`extensão`]: https://www.yiiframework.com/doc/guide/2.0/en/structure-extensions#creating-extensions
[`when`]: conditional-validation.md#when 
