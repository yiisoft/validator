# Настройка сообщений об ошибках

Чтобы использовать сообщение об ошибке, отличное от стандартного, передайте
собственное сообщение/шаблон при создании правила валидации:

```php
new Required(message: '{Property} is required.');
```

Некоторые правила имеют несколько сообщений об ошибках и переопределяются с
помощью различных соответствующих параметров. Их легко отличить от остальных
параметров по суффиксу `Message`:

```php
use Yiisoft\Validator\Rule\Length;

new Length(  
    min: 4,  
    max: 10,
    lessThanMinMessage: '{Property} is too short.',  
    greaterThanMaxMessage: '{Property} is too long.',  
);
```

Полный список поддерживаемых плейсхолдеров с описаниями доступен в PHPDoc
для каждого сообщения.

## Перевод сообщений об ошибках

Перевод сообщений об ошибках реализован с помощью пакета [Yii Translator],
используя в качестве источника сообщения на английском языке. Переводы
хранятся в обычном PHP-файле в виде ассоциативного массива, где ключи - это
исходные сообщения, а значения - переведенные сообщения.

Для использования переводов необходимо установить дополнительный пакет для
поддержки их чтения из PHP-файлов:

```shell
composer require yiisoft/translator-message-php
```

При использовании валидатора в экосистеме Yii (приложение использует [Yii
Config], а валидатор получается как зависимость через [Yii DI] контейнер)
переводы подключаются автоматически. В противном случае объект переводчика
необходимо создать и настроить вручную. Например так:

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

Вы можете проверить доступные переводы, посмотрев подпапки папки
`messages`. Если необходимый язык отсутствует, не стесняйтесь отправить
[PR].

## Перевод имен свойств

Almost all error templates have support for `{property}` and `{Property}`
placeholders which are substituted with an actual property name that was set
during rules configuration. The `{Property}` variant capitalizes the first
letter. By default, a property name is formatted as is. It can be acceptable
for English language (for example, `currentPassword is required.`), but when
using translations for error messages, it's better to provide an additional
property translation.

The simplest approach for properties defined via PHP attributes is to use
the `Label` attribute directly on the property. See [Configuring rules via
PHP attributes] for details.

Для решения именно этой задачи существует отдельный интерфейс
`PropertyTranslatorInterface`. Он поставляется с тремя реализациями из
коробки:

- `ArrayPropertyTranslator` - использует ассоциативный массив, где ключи -
это имена исходных свойств, а значения - их соответствующие переведенные
версии.  - `TranslatorPropertyTranslator` - использует [Yii Translator].  -
`NullPropertyTranslator` - фиктивный переводчик, возвращает имя свойства как
есть, без перевода.

Существует несколько способов использования переводчика свойств.

### Передача переводчика в экземпляр валидатора

Это вполне очевидно: просто создайте переводчик свойств, определите все
переводы и передайте их вновь созданному экземпляру валидатора. Пример для
русского языка:

```php
use Yiisoft\Validator\PropertyTranslator\ArrayPropertyTranslator;
use Yiisoft\Validator\Validator;

$propertyTranslator = new ArrayPropertyTranslator([
    'currentPassword' => 'Текущий пароль',
    'newPassword' => 'Новый пароль',
]);
$validator = new Validator(defaultPropertyTranslator: $propertyTranslator);
```

### Передача внутри объекта данных для проверки

Другой вариант - предоставление переводов в самом валидируемом объекте
данных. Этот подход может быть использован, например, для создания классов
форм.

Для этого случая существует еще один специальный интерфейс под названием
`PropertyTranslatorProviderInterface`, позволяющий валидатору извлекать
переводы из реализующих его объектов. Пример для русского языка:

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
[Настройка правил с помощью PHP атрибутов]: configuring-rules-via-php-attributes.md
[Yii Translator]: https://github.com/yiisoft/translator
[Yii Config]: https://github.com/yiisoft/config
[Yii DI]: https://github.com/yiisoft/di
