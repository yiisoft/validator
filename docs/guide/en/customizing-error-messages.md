# Customizing error messages

To use a non-default error message, pass a custom message/template when creating a validation rule. Generally the 
`message` option is responsible for storing error message:

```php
new Required(message: '{Property} is required.');
```

Some rules have multiple error messages and are overridden via different corresponding options.
It is easy to differentiate them from the rest of the parameters by `Message` suffix:

```php
use Yiisoft\Validator\Rule\Length;

new Length(  
    min: 4,  
    max: 10,
    lessThanMinMessage: '{Property} is too short.',  
    greaterThanMaxMessage: '{Property} is too long.',  
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

## Translating property names

Almost all error templates have support for a `{property}` placeholder which is substituted with an actual property
name that was set during rules configuration. By default, a property name is formatted as is. It can be acceptable for 
English language (for example, `currentPassword is required.`), but when using translations for error messages, it's 
better to provide an additional property translation.

There is a separate interface called `PropertyTranslatorInterface` to solve exactly this task. It ships with 3 
implementations out of the box:

- `ArrayPropertyTranslator` - uses an associative array, where keys are initial property names and values are their 
corresponding translated versions.
- `TranslatorPropertyTranslator` - uses [Yii Translator].
- `NullPropertyTranslator` - dummy translator, returns property name as is without translation.

There are several ways to use property translator.

### Passing translator to validator instance

It's quite self-explanatory, just create a property translator, define all translations, and pass it to the newly
created validator instance. An example for Russian language:

```php
use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\Validator;

$propertyTranslator = new ArrayPropertyTranslator([
    'currentPassword' => 'Текущий пароль',
    'newPassword' => 'Новый пароль',
]);
$validator = new Validator(defaultPropertyTranslator: $propertyTranslator);
```

### Passing within a data object for validation

Another option is providing translations in the validated data object itself. This approach can be used to create a form 
classes for example.

There is another special interface called `PropertyTranslatorProviderInterface` for this case allowing the validator to 
extract translations from the objects implementing it. An example for Russian language:

```php
use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\PropertyTranslatorInterface;
use Yiisoft\Validator\PropertyTranslatorProviderInterface;
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Validator;

final class ChangePasswordForm implements PropertyTranslatorProviderInterface  
{  
    public function __construct(  
        #[Required(message: '{Property} обязателен.')]  
        public string $currentPassword = '',  
  
        #[Length(  
            min: 8,
            skipOnEmpty: false,  
            lessThanMinMessage: '{Property} должен быть сложный, не менее 8 символов.'  
        )]  
        public string $newPassword = '',  
    ) {  
    }  
  
    public function getPropertyTranslator(): ?PropertyTranslatorInterface  
    {  
        return new ArrayPropertyTranslator([  
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
