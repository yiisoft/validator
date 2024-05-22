# Personalizando mensagens de erro

Para usar uma mensagem de erro não padrão, passe uma mensagem/template personalizado ao criar uma regra de validação. Geralmente a opção `message` é responsável por armazenar a mensagem de erro:

```php
new Required(message: '{attribute} is required.');
```

Algumas regras possuem diversas mensagens de erro e são substituídas por diferentes opções correspondentes.
É fácil diferenciá-las do resto dos parâmetros pelo sufixo `Message`:

```php
use Yiisoft\Validator\Rule\Length;

new Length(  
    min: 4,  
    max: 10,
    lessThanMinMessage: 'The {attribute} is too short.',  
    greaterThanMaxMessage: 'The {attribute} is too long.',  
);
```

Uma lista completa de placeholders suportados com descrição está disponível no [PHPDoc] para cada mensagem.

## Traduzindo mensagens de erro

A tradução de mensagens de erro é implementada com a ajuda do pacote [Yii Translator], usando mensagens em inglês
como fonte. As traduções são armazenadas em um arquivo PHP normal em formato de array associativo, onde as chaves são
mensagens e valores originais são traduções.

Para utilizar as traduções, é necessário instalar um pacote adicional para suporte à leitura de arquivos PHP:

```shell
composer require yiisoft/translator-message-php
```

Ao usar um validador dentro do ecossistema Yii (uma aplicação usa [Yii Config] e o validador é obtido como um
dependência via contêiner [Yii DI]), as traduções são conectadas automaticamente. Caso contrário, um objeto tradutor deve
ser criado e configurado manualmente. Por exemplo, assim:

```php
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\Message\Php\MessageSource;
use Yiisoft\Translator\SimpleMessageFormatter;
use Yiisoft\Translator\Translator;
use Yiisoft\Validator\Validator;

$translationsPath = '/app/vendor/yiisoft/validator/messages';
$categorySource = new CategorySource(
    Validator::DEFAULT_TRANSLATION_CATEGORY,
    new MessageSource($translationsPath),
    new SimpleMessageFormatter(),
);
$translator = new Translator(locale: 'ru');
$translator->addCategorySources($categorySource);

$validator = new Validator(translator: $translator);
```

Você pode verificar as traduções disponíveis visualizando as subpastas da pasta `messages`. Se um idioma necessário estiver
faltando, sinta-se à vontade para enviar um [PR].

## Traduzindo nomes de atributos

Quase todos os modelos de erro têm suporte para um espaço reservado `{attribute}` que é substituído por um nome de atributo real
que foi definido durante a configuração das regras. Por padrão, um nome de atributo é formatado como está. Pode ser aceitável para
idioma inglês (por exemplo, `currentPassword é obrigatório.`), mas ao usar traduções para mensagens de erro, é
melhor fornecer uma tradução de atributo adicional.

Existe uma interface separada chamada `AttributeTranslatorInterface` para resolver exatamente esta tarefa. Ele vem com 3
implementações prontas para uso:

- `ArrayAttributeTranslator` - usa um array associativo, onde as chaves são nomes de atributos iniciais e os valores são suas
versões traduzidas correspondentes.
- `TranslatorAttributeTranslator` - usa [Yii Translator].
- `NullAttributeTranslator` - tradutor fictício, retorna o nome do atributo como está, sem tradução.

Existem várias maneiras de usar o tradutor de atributos.

### Passando o tradutor para a instância do validador

É bastante autoexplicativo, basta criar um tradutor de atributos, definir todas as traduções e passá-lo para a nova
instância do validador criada. Um exemplo para o idioma russo:

```php
use Yiisoft\Validator\AttributeTranslator\ArrayAttributeTranslator;
use Yiisoft\Validator\Validator;

$attributeTranslator = new ArrayAttributeTranslator([
    'currentPassword' => 'Текущий пароль',
    'newPassword' => 'Новый пароль',
]);
$validator = new Validator(defaultAttributeTranslator: $attributeTranslator);
```

### Passando dentro de um objeto de dados para validação

Outra opção é fornecer traduções no próprio objeto de dados validado. Esta abordagem pode ser usada para criar um formulário
classes por exemplo.

Existe outra interface especial chamada `AttributeTranslatorProviderInterface` para este caso, permitindo ao validador
extrair traduções dos objetos que o implementam. Um exemplo para o idioma russo:

```php
use Yiisoft\Validator\AttributeTranslator\ArrayAttributeTranslator;
use Yiisoft\Validator\AttributeTranslatorInterface;
use Yiisoft\Validator\AttributeTranslatorProviderInterface;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

final class ChangePasswordForm implements AttributeTranslatorProviderInterface  
{  
    public function __construct(  
        #[Required(message: '{attribute} обязателен для ввода.')]  
        public string $currentPassword = '',  
  
        #[Length(  
            min: 8,
            skipOnEmpty: false,  
            lessThanMinMessage: '{attribute} должен быть сложный, не менее 8 символов.'  
        )]  
        public string $newPassword = '',  
    ) {  
    }  
  
    public function getAttributeTranslator(): ?AttributeTranslatorInterface  
    {  
        return new ArrayAttributeTranslator([  
            'currentPassword' => 'Текущий пароль',  
            'newPassword' => 'Новый пароль',  
        ]);  
    }  
}

$form = new ChangePasswordForm();    
$result = (new Validator())->validate($form);
```

[PHPDoc]: https://www.phpdoc.org/
[Yii Translator]: https://github.com/yiisoft/translator
[Yii Config]: https://github.com/yiisoft/config
[Yii DI]: https://github.com/yiisoft/di
[PR]: https://github.com/yiisoft/validator/pulls
