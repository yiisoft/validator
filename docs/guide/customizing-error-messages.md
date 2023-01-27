# Customizing error messages

To use a non-default error message, pass a custom message/template when creating a validation rule. Generally the 
`message` option is responsible for storing error message:

```php
new Required(message: '{attribute} is required.');
```

Some rules, however, have multiple error messages and, therefore, are overridden via different corresponding options. But 
it's easy to differentiate them from the rest of the parameters by `Message` prefix:

```php
new HasLength(  
    min: 4,  
    max: 10,
    lessThanMinMessage: 'The {attribute} is too short.',  
    greaterThanMaxMessage: 'The {attribute} is too long.',  
);
```

A full list of supported placeholders with descriptions is available in PHPDoc for each message.

## Translating error messages

The translation of error messages is implemented with the help of [Yii Translator] package, using messages in English 
language as a source. The translations are stored in a regular PHP file in associative array form, where keys are 
original messages and values are translations. 

To use the translations, it's required to install an additional package for support of reading them from PHP files:

```shell
composer require yiisoft/translator-message-php
```

When using a validator within the Yii ecosystem (an application uses [Yii Config] and the validator is obtained as a 
dependency via [Yii DI] container), the translations are plugged in automatically. Otherwise, a translator object must 
be created and configured manually. For example, like this:

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

## Translating attribute names

Almost all error templates have support for `{attribute}` placeholder which is substituted with an actual attribute name 
that was set during rules configuration. By default, an attribute name is formatted as is. It can be acceptable for 
English language (for example, `currentPassword is required.`), but when using translations for error messages, it's 
better to provide an additional attribute translation.

There is a separate interface called `AttributeTranslatorInterface` to solve exactly this task. It ships with 3 
implementations out of the box:

- `ArrayAttributeTranslator` - uses an associative array, where keys are initial attribute names and values are their 
corresponding translated versions.
- `TranslatorAttributeTranslator` - uses [Yii Translator].
- `NullAttributeTranslator` - dummy translator, returns attribute name as is without translation.

There are several ways to use attribute translator.

### Passing translator to validator instance

It's quite self-explanatory, just create an attribute translator, define all translations, and pass it to the newly
created validator instance. An example for the russian language:

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

Another option is providing translations in the validated data object itself. This approach can be used to create a form 
classes for example.

There is another special interface called `AttributeTranslatorProviderInterface` for this case allowing the validator to 
extract translations from the objects implementing it. An example for the russian language:

```php
final class ChangePasswordForm implements AttributeTranslatorProviderInterface  
{  
    public function __construct(  
        #[Required(message: '{attribute} обязателен для ввода.')]  
        public string $currentPassword = '',  
  
        #[HasLength(  
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

$form = new PasswordForm();    
$result = (new Validator())->validate($form);
```

[PR]: https://github.com/yiisoft/validator/pulls
[Yii Translator]: https://github.com/yiisoft/translator
[Yii Config]: https://github.com/yiisoft/config
[Yii DI]: https://github.com/yiisoft/di
