# `Callback` - um wrapper em torno de [`callables`]

Esta regra permite a validação do valor do atributo atual (mas não limitado a ele) com uma condição arbitrária dentro de um
callable. A vantagem é que não há necessidade de criar uma regra e um manipulador personalizado separados.

Uma condição pode estar dentro de:

- Função [`callables`] autônoma.
- Classe `Callable`.
- Método [`DTO`] (objeto de transferência de dados).

A desvantagem de usar funções independentes e métodos [`DTO`] é a falta da capacidade de reutilização. Então eles são principalmente úteis
para algumas condições específicas não repetitivas. A reutilização pode ser alcançada com classes que podem ser chamadas, mas dependendo de outros
fatores (a necessidade de parâmetros adicionais, por exemplo), pode ser uma boa ideia criar um relatório completo
[regra personalizada] com um manipulador separado.

A sintaxe da função de retorno de chamada é a seguinte:

```php
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\ValidationContext;

function (mixed $value, Callback $rule, ValidationContext $context): Result;
```

onde:

- `$value` é o valor validado;
- `$rule` é uma referência à regra `Callback` original;
- `$context` é o contexto da validação;
- o valor retornado é uma instância do resultado da validação com ou sem erros.

## Usando como uma função

Um exemplo de passagem de um callback autônomo para uma regra `Callback`:

```php
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\ValidationContext;

new Callback(
    static function (mixed $value, Callback $rule, ValidationContext $context): Result {
        // The actual validation code.
        
        return new Result();
    },
);
```

## Exemplos

### Validação de valor

A regra `Callback` pode ser usada para adicionar validação ausente nas regras integradas para o valor de um único atributo. Abaixo está o
exemplo verificando se um valor é uma string [YAML] válida (requer adicionalmente a extensão [PHP `yaml`]):

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

> **Nota:** Processar entradas de usuários não confiáveis com [`yaml_parse()`] pode ser perigoso com certas configurações. Consulte
> a documentação [`yaml_parse()`] para mais detalhes.

### Uso do contexto de validação para validar vários atributos dependendo uns dos outros

No exemplo abaixo, os 3 ângulos são validados como graus para formar
um triângulo válido:

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
            $angleA = $context->getDataSet()->getAttributeValue('angleA');
            $angleB = $context->getDataSet()->getAttributeValue('angleB');
            $angleC = $context->getDataSet()->getAttributeValue('angleC');
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

### Substituindo o código padrão por regras separadas e [`when`]

No entanto, alguns casos de uso de contexto de validação podem levar a um código padrão:

```php
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\ValidationContext;

static function (mixed $value, Callback $rule, ValidationContext $context): Result {
    if ($context->getDataSet()->getAttributeValue('married') === false) {
        return new Result();
    }
    
    $spouseAge = $context->getDataSet()->getAttributeValue('spouseAge');
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

Eles podem ser reescritos usando múltiplas regras e validação condicional, tornando o código mais intuitivo. Podemos usar regras embutidas
sempre que possível:

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
            return $context->getDataSet()->getAttributeValue('married') === true;
        },
    ),
];
```

## Usando como método de um objeto

### Para propriedades

Ao usar como um atributo PHP, defina o método de um objeto como um retorno de chamada:

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

A sintaxe é a mesma de uma função normal. Observe que não há restrições nos níveis de visibilidade e modificadores estáticos,
todos eles podem ser usados (`public`, `protected`, `private`, `static`).

Usar um argumento `callback` em vez de um [`método`] com atributos PHP é proibido devido à restrições da linguagem PHP atual
(um retorno de chamada não pode estar dentro de um atributo PHP).

### Para todo o objeto

Também é possível verificar todo o objeto:

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

Observe o uso do valor da propriedade (`$this->yaml`) em vez do argumento do método (`$value`).

## Usando uma classe que pode ser chamada

Uma classe que implementa `__invoke()` também pode ser usada como callable:

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

A sintaxe é a mesma de uma função regular.

Usando em regras (observe que uma nova instância deve ser passada, não um nome de classe):

```php
use Yiisoft\Validator\Rule\Callback;

$rules = [
    'yaml' => new Callback(new YamlCallback()),
];
``` 

## Atalho para uso com validator

Ao usar com o validator e as configurações padrão da regra `Callback`, uma declaração de regra pode ser omitida, portanto, apenas incluir um
callable é suficiente. Será normalizado automaticamente antes da validação:

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

Ou pode ser definido como um array de outras regras:

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

[`callables`]: https://www.php.net/manual/pt_BR/language.types.callable.php
[regra personalizada]: creating-custom-rules.md
[YAML]: https://en.wikipedia.org/wiki/YAML
[PHP `yaml`]: https://www.php.net/manual/pt_BR/book.yaml.php
[`yaml_parse()`]: https://www.php.net/manual/pt_BR/function.yaml-parse.php
[`DTO`]: https://pt.wikipedia.org/wiki/Data_transfer_object
[`when`]: conditional-validation.md#when
[`método`]: built-in-rules-callback.md#para-propriedades
