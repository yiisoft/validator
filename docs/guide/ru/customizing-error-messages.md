# Customizing error messages
# Настройка сообщений об ошибках

To use a non-default error message, pass a custom message/template when creating a validation rule.
Чтобы использовать сообщение об ошибке, отличное от стандартного, передайте собственное сообщение/шаблон при создании правила валидации.

Generally the `message` option is responsible for storing error message:
В целом, параметр `message` отвечает за сохранение сообщения об ошибке:

```php
new Required(message: '{attribute} is required.');
```

Some rules have multiple error messages and are overridden via different corresponding options.
Некоторые правила имеют несколько сообщений об ошибках и переопределяются с помощью различных соответствующих параметров.
It is easy to differentiate them from the rest of the parameters by `Message` suffix:
Их легко отличить от остальных параметров по суффиксу `Message`.

```php
use Yiisoft\Validator\Rule\Length;

new Length(  
    min: 4,  
    max: 10,
    lessThanMinMessage: 'The {attribute} is too short.',  
    greaterThanMaxMessage: 'The {attribute} is too long.',  
);
```

A full list of supported placeholders with descriptions is available in PHPDoc for each message.
Полный список поддерживаемых плейсхолдеров с описаниями доступен в PHPDoc для каждого сообщения.

## Translating error messages
## Перевод сообщений об ошибках

The translation of error messages is implemented with the help of [Yii Translator] package, using messages in English language as a source. 
Перевод сообщений об ошибках реализован с помощью пакета [Yii Translator], используя в качестве источника сообщения на английском языке.
The translations are stored in a regular PHP file in associative array form, where keys are 
original messages and values are translations. 
Переводы хранятся в обычном PHP-файле в виде ассоциативного массива, где ключи - это исходные сообщения, а значения - переведенные сообщения.

To use the translations, it's required to install an additional package for support of reading them from PHP files:
Для использования переводов необходимо установить дополнительный пакет для поддержки их чтения из PHP-файлов:

```shell
composer require yiisoft/translator-message-php
```

When using a validator within the Yii ecosystem (an application uses [Yii Config] and the validator is obtained as a dependency via [Yii DI] container), the translations are plugged in automatically. 
При использовании валидатора в экосистеме Yii (приложение использует [Yii Config], а валидатор получается как зависимость через [Yii DI] контейнер) переводы подключаются автоматически.
Otherwise, a translator object must be created and configured manually.
В противном случае объект переводчика необходимо создать и настроить вручную.
For example, like this:
Например так:

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

You can check the available translations by viewing subfolders of the `messages` folder. If a required language is 
missing, feel free to submit a [PR].
Вы можете проверить доступные переводы, посмотрев подпапки папки `messages`.
Если необходимый язык отсутствует, не стесняйтесь отправить [PR].

## Translating attribute names
## Перевод имен атрибутов

Almost all error templates have support for an `{attribute}` placeholder which is substituted with an actual attribute name that was set during rules configuration.
Почти все шаблоны ошибок поддерживают плейсхолдер `{attribute}`, который заменяется фактическим именем атрибута, заданным во время настройки правил.
By default, an attribute name is formatted as is.
По умолчанию имя атрибута форматируется без изменений.
It can be acceptable for English language (for example, `currentPassword is required.`), but when using translations for error messages, it's better to provide an additional attribute translation.
Это может быть приемлемо для английского языка (например, `currentPassword is required.`), но при использовании перевода сообщений об ошибках лучше предоставить дополнительный перевод атрибута.

There is a separate interface called `AttributeTranslatorInterface` to solve exactly this task.
Для решения именно этой задачи существует отдельный интерфейс `AttributeTranslatorInterface`.
It ships with 3 implementations out of the box:
Он поставляется с тремя реализациями из коробки:

- `ArrayAttributeTranslator` - uses an associative array, where keys are initial attribute names and values are their corresponding translated versions.
- `ArrayAttributeTranslator` - использует ассоциативный массив, где ключи - это имена исходных атрибутов, а значения - их соответствующие переведенные версии.
- `TranslatorAttributeTranslator` - uses [Yii Translator].
- `TranslatorAttributeTranslator` - использует [Yii Translator].
- `NullAttributeTranslator` - dummy translator, returns attribute name as is without translation.
- `NullAttributeTranslator` - фиктивный переводчик, возвращает имя атрибута как есть, без перевода.


There are several ways to use attribute translator.
Существует несколько способов использования переводчика атрибутов.

### Passing translator to validator instance
### Передача переводчика в экземпляр валидатора

It's quite self-explanatory, just create an attribute translator, define all translations, and pass it to the newly created validator instance.
Это вполне очевидно: просто создайте переводчик атрибутов, определите все переводы и передайте их вновь созданному экземпляру валидатора.
An example for Russian language:
Пример для русского языка:

```php
use Yiisoft\Validator\AttributeTranslator\ArrayAttributeTranslator;
use Yiisoft\Validator\Validator;

$attributeTranslator = new ArrayAttributeTranslator([
    'currentPassword' => 'Текущий пароль',
    'newPassword' => 'Новый пароль',
]);
$validator = new Validator(defaultAttributeTranslator: $attributeTranslator);
```

### Passing within a data object for validation
### Передача внутри объекта данных для проверки

Another option is providing translations in the validated data object itself.
Другой вариант - предоставление переводов в самом валидируемом объекте данных.
This approach can be used to create a form classes for example.
Этот подход может быть использован, например, для создания классов форм.

There is another special interface called `AttributeTranslatorProviderInterface` for this case allowing the validator to extract translations from the objects implementing it.
Для этого случая существует еще один специальный интерфейс под названием `AttributeTranslatorProviderInterface`, позволяющий валидатору извлекать переводы из реализующих его объектов.
An example for Russian language:
Пример для русского языка:

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

[PR]: https://github.com/yiisoft/validator/pulls
[Yii Translator]: https://github.com/yiisoft/translator
[Yii Config]: https://github.com/yiisoft/config
[Yii DI]: https://github.com/yiisoft/di
