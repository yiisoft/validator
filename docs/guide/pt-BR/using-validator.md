# Usando validações

`Validator` permite verificar dados em qualquer formato. Aqui estão alguns dos casos de uso mais comuns.

## Dados

### Valor único

No caso mais simples, o validador pode ser usado para verificar um único valor:

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Validator;

$value = 'mrX';
$rules = [
    new Length(min: 4, max: 20),
    new Regex('~^[a-z_\-]*$~i'),
];
$result = (new Validator())->validate($value, $rules);
```

> **Nota:** Use a regra [`Each`] para validar vários valores do mesmo tipo.

### Array

É possível validar um array como um todo ou por itens individuais. Por exemplo:

```php
use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

$data = [
    'name' => 'John',
    'age' => 17,
    'email' => 'john@example.com',
    'phone' => null,
];
$rules = [
    // As regras que não estão relacionadas a um atributo específico

    // Pelo menos um dos atributos ("email" e "phone") deve ser passado e ter valor não vazio.
    new AtLeast(['email', 'phone']),

    // As regras relacionadas a um atributo específico.

    'name' => [
        // O nome é obrigatório (deve ser passado e ter valor não vazio).
        new Required(),
        // O comprimento do nome não deve ser inferior a 2 caracteres.
        new Length(min: 2),
    ],
    'age' => new Number(min: 21), // A idade deve ser de pelo menos 21 anos.
    'email' => new Email(), // O email deve ser um endereço de email válido.
];
$result = (new Validator())->validate($data, $rules);
```

> **Nota:** Use a regra [`Nested`] para validar arrays aninhados e a regra [`Each`] para validar vários arrays.

### Objeto

Semelhante aos arrays, é possível validar um objeto como um todo ou por propriedades individuais.

Para objetos existe uma opção adicional para configurar a validação com atributos PHP que permite não passar as regras
separadamente de forma explícita (passar apenas o objeto em si é suficiente). Por exemplo:

```php
use Yiisoft\Validator\Rule\AtLeast;
use Yiisoft\Validator\Rule\Email;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

#[AtLeast(['email', 'phone'])]
final class Person
{
    public function __construct(
        #[Required]
        #[Length(min: 2)]
        public readonly ?string $name = null,

        #[Number(min: 21)]
        public readonly ?int $age = null,

        #[Email]
        public readonly ?string $email = null,

        public readonly ?string $phone = null,
    ) {
    }
}

$person = new Person(name: 'John', age: 17, email: 'john@example.com', phone: null);
$result = (new Validator())->validate($person);
```

> **Notas:** 
>- [Propriedades somente leitura] são suportadas apenas a partir do PHP 8.1.
>- Use a regra [`Nested`] para validar objetos relacionados e a regra [`Each`] para validar vários objetos.

### Conjunto de dados personalizado

Na maioria das vezes, a criação de um conjunto de dados personalizados não é necessário devido aos conjuntos de dados integrados e à normalização automática de
todos os tipos durante a validação. No entanto, isto pode ser útil, por exemplo, para alterar um valor padrão para determinados atributos:

```php
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Validator;

final class MyArrayDataSet implements DataSetInterface
{
    public function __construct(private array $data = [],)
    {
    }

    public function getAttributeValue(string $attribute): mixed
    {
        if ($this->hasAttribute($attribute)) {
            return $this->data[$attribute];
        }

        return $attribute === 'name' ? '' : null;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function hasAttribute(string $attribute): bool
    {
        return array_key_exists($attribute, $this->data);
    }
}

$data = new MyArrayDataSet([]);
$rules = ['name' => new Length(min: 2), 'age' => new Number(min: 21)];
$result = (new Validator())->validate($data, $rules);
```

## Regras

### Passando uma regra única

Para uma regra única existe a opção de omitir o array:

```php
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Validator;

$value = 7;
$rule = new Number(min: 42);
$result = (new Validator())->validate($value, $rule);
```

### Fornecendo regras via objeto dedicado

Poderia ajudar a reutilização do mesmo conjunto de regras em locais diferentes. Duas maneiras são possíveis - usando atributos PHP
e especificando explicitamente por meio da implementação do método de interface.

#### Usando atributos PHP

Neste caso, as regras serão analisadas automaticamente, sem necessidade de fazer nada a mais.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Validator;

final class PersonRulesProvider implements RulesProviderInterface
{
    #[Length(min: 2)]
    public string $name;

    #[Number(min: 21)]
    protected int $age;
}

$data = ['name' => 'John', 'age' => 18];
$rulesProvider = new PersonRulesProvider();
$result = (new Validator())->validate($data, $rulesProvider);
```

#### Usando implementação de método de interface

O fornecimento de regras por meio da implementação do método de interface tem prioridade sobre os atributos PHP. Então, caso ambos estejam presentes,
os atributos serão ignorados sem causar uma exceção.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Validator;

final class PersonRulesProvider implements RulesProviderInterface
{
    #[Length(min: 2)] // Will be silently ignored.
    public string $name;

    #[Number(min: 21)] // Will be silently ignored.
    protected int $age;

    public function getRules() : iterable
    {
        return ['name' => new Length(min: 2), 'age' => new Number(min: 21)];
    }
}

$data = ['name' => 'John', 'age' => 18];
$rulesProvider = new PersonRulesProvider();
$result = (new Validator())->validate($data, $rulesProvider);
```

### Fornecendo regras por meio do objeto de dados

Dessa forma, as regras são fornecidas além dos dados no mesmo objeto. Apenas a implementação do método de interface é
suportado. Observe que o argumento `rules` é `null` na chamada do método `validate()`.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Validator;

final class Person implements RulesProviderInterface
{
    #[Length(min: 2)] // Not supported for using with data objects. Will be silently ignored.
    public string $name;

    #[Number(min: 21)] // Not supported for using with data objects. Will be silently ignored.
    protected int $age;

    public function getRules(): iterable
    {
        return ['name' => new Length(min: 2), 'age' => new Number(min: 21)];
    }
}

$data = new Person(name: 'John', age: 18);
$result = (new Validator())->validate($data);
```

[`Each`]: built-in-rules-each.md
[`Nested`]: built-in-rules-nested.md
[Propriedades somente leitura]: https://www.php.net/manual/pt_BR/language.oop5.properties.php#language.oop5.properties.readonly-properties
