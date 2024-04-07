# Configurando regras via atributos PHP

O recurso [Attributes] introduzido no PHP 8 permite uma forma alternativa de configurar regras. Se entidades/modelos com
suas relações são representadas como classes [DTO], os atributos possibilitam o uso de tais classes para fornecer regras.
As regras são definidas acima das próprias propriedades, o que alguns desenvolvedores podem achar mais conveniente em termos
de legibilidade.

## Configurando para uma entidade / modelo única

Dada uma entidade/modelo única `User`:

```php
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

[
    'name' => [
        new Required(),
        new Length(min: 1, max: 50),
    ],
    'age' => [
        new Integer(min: 18, max: 100),
    ],
]
```

o equivalente dos atributos PHP será:

```php
use JetBrains\PhpStorm\Deprecated;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

final class User
{
    public function __construct(
        // Multiple attributes.
        #[Required]
        #[Length(min: 1, max: 50)]
        // Can be combined with other attributes not related with rules.
        #[Deprecated]
        private readonly string $name,
        // Single attribute.
        #[Integer(min: 18, max: 100)]
        private readonly int $age,
    ) {
    }
}
```

Este exemplo usa o recurso [promoção de propriedade do construtor] introduzido no PHP 8. Os atributos também podem
ser usados com propriedades regulares:

```php
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;

final class User
{
    // Multiple attributes.
    #[Required]
    #[Length(min: 1, max: 50)]
    public readonly string $name;

    // Single attribute.
    #[Integer(min: 18, max: 100)]
    public readonly int $age;
}
```

> **Nota:** [propriedades somente leitura] são suportadas apenas a partir do PHP 8.1.

## Configurando para múltiplas entidades/modelos com relações

Um exemplo de conjunto de regras para uma postagem de blog configurada apenas por meio de arrays:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Url;

[
    new Nested([
        'title' => [
            new Length(min:1, max: 255),
        ],
        // One-to-one relation.
        'author' => new Nested([
            'name' => [
                new Required(),
                new Length(min: 1, max: 50),
            ],
            'age' => [
                new Integer(min: 18, max: 100),
            ],
        ]),
        // One-to-many relation.
        'files' => new Each([
            new Nested([
                'url' => [new Url()],
            ]),
        ]),
    ]),
];
```

Pode ser aplicado a tais classes DTO para obter o mesmo efeito:

```php
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Url;

final class Post
{
    #[Length(min: 1, max: 255)]
    public string $title;

    // "Nested" can be used without arguments, but make sure to fill the value with the instance in this case (here it's
    // filled right in the constructor).
    #[Nested]
    public Author|null $author = null;

    // Passing instances is available only since PHP 8.1.
    #[Each(new Nested(File::class))]
    public array $files = [];

    public function __construct()
    {
        $this->author = new Author();
    }
}

final class Author
{
    #[Required]
    #[Length(min: 1, max: 50)]
    public string $name;

    #[Integer(min: 18, max: 100)]
    public int $age;
}

// Some rules, like "Nested" can be also configured through the class attribute.

#[Nested(['url' => new Url()])]
final class File
{
    public string $url;
}
```

Para uma melhor compreensão do conceito de relações, recomenda-se a leitura da documentação [Nested] e [Each].

## Traits

Atributos também podem ser usados em traits. Pode ser útil reutilizar o mesmo conjunto de propriedades com regras idênticas:

```php
use Yiisoft\Validator\Rule\Length;

trait TitleTrait
{
    #[Length(max: 255)]
    public string $title;
}

final class BlogPost
{
    use TitleTrait;
}

final class WikiArticle
{
    use TitleTrait;
}
```

## Herança

A herança é suportada, mas há algumas coisas a serem lembradas:

```php
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

class Car
{
    #[Required]
    #[Length(min: 1, max: 50)]
    public string $name;
    
    #[Required]
    #[BooleanValue]
    public $used;
    
    #[Required]
    #[Number(max: 2000)]
    public float $weight;     
}

class Truck extends Car
{       
    public string $name;
    
    #[Number(max: 3500)]
    public float $weight;      
}
```

Neste caso o conjunto de regras para `Truck` será:

```php
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

[
    'used' => [
        new Required(),
        new BooleanValue(),
    ],
    'weight' => [
        new Number(max: 3500),
    ],
];
```

Entao, para resumir:

- As regras pai para propriedades substituídas são completamente ignoradas, apenas as da classe filha são obtidas.
- Todas as regras pai para propriedades que não são substituídas na classe filha são obtidas integralmente.

Quanto aos dados, os valores padrão definidos na classe filha têm precedência.

## Adicionando suporte de atributos a regras personalizadas

Para anexar regras às propriedades do DTO ou a todo o DTO, o atributo `Attribute` deve ser adicionado à
classe personalizada. E para que as regras sejam obtidas dos atributos, elas devem implementar a classe `RuleInterface`.

Para regras `Composite` personalizadas, você só precisa adicionar o atributo:

```php
use Attribute;
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;

// Make sure to add this because attribute inheritance is not supported.
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
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
```

Exemplo de regra personalizada:

```php
use Attribute;
use Yiisoft\Validator\RuleInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Yaml implements RuleInterface
{
    public function __construct(
        public string $incorrectInputMessage = 'Value must be a string. {type} given.',
        public string $message = 'The value is not a valid YAML.',
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

Para permitir a anexação à classe, modifique a definição do atributo assim:

```php
use Attribute;
use Yiisoft\Validator\RuleInterface;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Yaml implements RuleInterface 
{
    // ...
}
```

## Limitações e soluções alternativas

### Instâncias

Passar instâncias no escopo de atributos só é possível a partir do PHP 8.1. Isso significa usar atributos para regras complexas
como `Composite`, `Each` e `Nested` ou regras que usam instâncias como argumentos podem ser problemáticas com o PHP 8.0.

A primeira solução alternativa é atualizar para o PHP 8.1 - isso é bastante simples, pois é uma versão secundária. Ferramentas como
[Rector] pode facilitar o processo de atualização da base de código automatizando tarefas de rotina.

Se isso não for uma opção, você poderá usar outras formas de fornecer regras, como provedores de regras:

```php
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\Url;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Validator;

final class Post
{
    public function __construct(
        private string $title,
        private Author|null $author = null,
        private array $files = [],
    ) {
    }
}

final class Author
{
    public function __construct(
        private string $name,
        private int $age,
    ) {
    }
}

final class File
{
    private string $url;
}

final class PostRulesProvider implements RulesProviderInterface
{
    public function getRules(): array
    {
        return [
            new Nested([
                'title' => new Length(min:1, max: 255),
                'author' => [
                    'name' => [
                        new Required(),
                        new Length(min: 1, max: 50),
                    ],
                    'age' => new Integer(min: 18, max: 100),
                ],
                'files.*.url' => new Url(),
            ]),
        ];
    }
}

$post = new Post(title: 'Hello, world!');
$postRulesProvider = new PostRulesProvider();
$validator = (new Validator())->validate($post, $postRulesProvider);
```

Para regras sem relações, em vez de usar `Composite` diretamente, crie uma classe filha que se estenda a partir dela e coloque a
regras nela. Não se esqueça de adicionar o atributo support.

```php
use Attribute;
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\Rule\Count;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Integer;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
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

final class User
{
    public function __construct(
        private string $name,
        #[RgbColorRuleSet]
        private array $avatarBackgroundColor,
    ) {
    }
}
```

A regra `Nested` pode ser usada sem argumentos, veja este [exemplo](#Configurando-para-uma-entidade--modelo-única) acima.

### Callables

A tentativa de usar callables dentro do escopo de um atributo causará o erro. Isso significa que usar [when] para
[validação condicional] ou o argumento `callback` para a regra `Callback` não funcionará.

As soluções alternativas são:

- `Composite` ou o provedor de regras descrito na seção [Instâncias] também caberá aqui.
- Crie uma [regra personalizada].
- Para a regra `Callback` em particular, é possível substituir um retorno de chamada por uma [referência de método].

### Chamadas de funções/métodos

As chamadas de função e de método não são suportadas no escopo de um atributo. Se a intenção é chamar uma função /
método para validação - use uma regra `Callback` com [referência de método]. Caso contrário, as opções restantes são:

- Use `Composite` ou provedor de regras descrito na seção [Instâncias].
- Crie uma [regra personalizada].

## Usando regras

Bem, as regras estão configuradas. Qual é o próximo passo? Podemos:

- Passe-os para validação imediatamente.
- Ajuste a análise de regras (propriedades puláveis, usando cache).
- Use-os para outra coisa (por exemplo, para exportar suas opções).

Vamos usar uma postagem de blog novamente para demonstração, mas em uma versão ligeiramente abreviada:

```php
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Required;

final class Post
{
    public function __construct(
        #[Length(min: 1, max: 255)]
        private string $title,

        #[Nested(Author::class)]
        private Author|null $author,
    ) {
    }
}

final class Author
{
    public function __construct(
        #[Required]
        #[Length(min: 1, max: 50)]
        private string $name,

        #[Integer(min: 18, max: 100)]
        private int $age,
    ) {
    }
}
```

### Passando junto com dados para validação

Provavelmente, uma das maneiras mais limpas é passar instâncias de DTO com regras e dados declarados. Esta forma não requer
qualquer configuração adicional:

```php
use Yiisoft\Validator\Validator;

$post = new Post(
    title: 'Hello, world!',
    author: new Author(
        name: 'John',
        age: 18,
    ),
);
$result = (new Validator())->validate($post) // Note `$rules` argument is `null` here.
```

### Passando separadamente para validação

Pode ser útil usar a classe para analisar regras e fornecer dados separadamente:

```php
use Yiisoft\Validator\Validator;

$data = [
    'title' => 'Hello, world!',
    'author' => [
        'name' => 'John',
        'age' => 18,
    ],
];
$result = (new Validator())->validate($data, Post::class);
```

Os dados não precisam estar dentro de um array, o objetivo deste exemplo é mostrar que eles estão isolados das regras.

### Ajustando a análise de regras

Os dados passados para validação como um objeto serão automaticamente normalizados para `ObjectDataSet`. No entanto, você pode
envolve o objeto validado com este conjunto para permitir alguma configuração adicional:

```php
use Yiisoft\Validator\DataSet\ObjectDataSet;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Validator;

final class Post
{
    // Will be skipped from parsing rules declared via PHP attributes.
    private $author;

    public function __construct(
        #[Length(min: 1, max: 255)]
        public string $title,

        #[Length(min: 1)]
        protected $content,
    ) {
    }
}

$post = new Post(title: 'Hello, world!', content: 'Test content.');
$dataSet = new ObjectDataSet(
    $post,
    propertyVisibility: ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED,
    useCache: false,
);
$result = (new Validator())->validate($dataSet);
```

Alguns casos extremos, como ignorar as propriedades estáticas do DTO, exigem o uso de `AttributeRulesProvider`. Depois de inicializá-lo
pode ser passado para validação imediatamente - não há necessidade de extrair regras manualmente de antemão.

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;
use Yiisoft\Validator\Validator;

final class Post
{
    // Will be skipped from parsing rules declared via PHP attributes.
    private static $cache = [];

    public function __construct(
        #[Length(min: 1, max: 255)]
        private string $title,
    ) {
    }
}

$post = new Post(title: 'Hello, world!');
$rules = new AttributesRulesProvider(Post::class, skipStaticProperties: true);
$validator = (new Validator())->validate($post, $rules);
```

### Usando regras fora do escopo do validador

Digamos que queremos extrair todas as regras para exportar suas opções para o lado do cliente para posterior implementação da validação
no frontend:

```php
use Yiisoft\Validator\Helper\RulesDumper;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;
use Yiisoft\Validator\Validator;

final class Post
{
    public function __construct(
        #[Length(min: 1, max: 255)]
        private string $title,
    ) {
    }
}

// The rules need to be extracted manually first.
$rules = (new AttributesRulesProvider(Post::class))->getRules();
$validator = (new Validator())->validate([], $rules);
$options = RulesDumper::asArray($rules);
```

[Attributes]: https://www.php.net/manual/en/language.attributes.overview.php
[DTO]: https://en.wikipedia.org/wiki/Data_transfer_object
[promoção de propriedade do construtor]: https://www.php.net/manual/en/language.oop5.decon.php#language.oop5.decon.constructor.promotion
[propriedades somente leitura]: https://www.php.net/manual/en/language.oop5.properties.php#language.oop5.properties.readonly-properties
[Nested]: built-in-rules-nested.md
[Each]: built-in-rules-each.md
[Rector]: https://github.com/rectorphp/rector
[when]: conditional-validation.md#when
[validação condicional]: conditional-validation.md
[Instâncias]: #instâncias
[regra personalizada]: creating-custom-rules.md
[referência de método]: built-in-rules-callback.md#para-propriedades