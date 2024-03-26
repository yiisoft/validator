# Настройка сообщений об ошибках

Чтобы использовать сообщение об ошибке, отличное от стандартного, передайте собственное сообщение/шаблон при создании правила валидации.

В целом, параметр `message` отвечает за сохранение сообщения об ошибке:

```php
new Required(message: '{attribute} is required.');
```

Некоторые правила имеют несколько сообщений об ошибках и переопределяются с помощью различных соответствующих параметров.
Их легко отличить от остальных параметров по суффиксу `Message`:

```php
use Yiisoft\Validator\Rule\Length;

new Length(  
    min: 4,  
    max: 10,
    lessThanMinMessage: 'The {attribute} is too short.',  
    greaterThanMaxMessage: 'The {attribute} is too long.',  
);
```

Полный список поддерживаемых плейсхолдеров с описаниями доступен в PHPDoc для каждого сообщения.

## Перевод сообщений об ошибках

Перевод сообщений об ошибках реализован с помощью пакета [Yii Translator], используя в качестве источника сообщения на английском языке.
Переводы хранятся в обычном PHP-файле в виде ассоциативного массива, где ключи - это исходные сообщения, а значения - переведенные сообщения.

Для использования переводов необходимо установить дополнительный пакет для поддержки их чтения из PHP-файлов:

```shell
composer require yiisoft/translator-message-php
```

При использовании валидатора в экосистеме Yii (приложение использует [Yii Config], а валидатор получается как зависимость через [Yii DI] контейнер) переводы подключаются автоматически.
В противном случае объект переводчика необходимо создать и настроить вручную.
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

Вы можете проверить доступные переводы, посмотрев подпапки папки `messages`.
Если необходимый язык отсутствует, не стесняйтесь отправить [PR].

## Перевод имен атрибутов

Почти все шаблоны ошибок поддерживают плейсхолдер `{attribute}`, который заменяется фактическим именем атрибута, заданным во время настройки правил.
По умолчанию имя атрибута форматируется без изменений.
Это может быть приемлемо для английского языка (например, `currentPassword is required.`), но при использовании перевода сообщений об ошибках, лучше предоставить дополнительный перевод атрибута.

Для решения именно этой задачи существует отдельный интерфейс `AttributeTranslatorInterface`.
Он поставляется с тремя реализациями из коробки:

- `ArrayAttributeTranslator` - использует ассоциативный массив, где ключи - это имена исходных атрибутов, а значения - их соответствующие переведенные версии.
- `TranslatorAttributeTranslator` - использует [Yii Translator].
- `NullAttributeTranslator` - фиктивный переводчик, возвращает имя атрибута как есть, без перевода.

Существует несколько способов использования переводчика атрибутов.

### Передача переводчика в экземпляр валидатора

Это вполне очевидно: просто создайте переводчик атрибутов, определите все переводы и передайте их вновь созданному экземпляру валидатора.
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

### Передача внутри объекта данных для проверки

Другой вариант - предоставление переводов в самом валидируемом объекте данных.
Этот подход может быть использован, например, для создания классов форм.

Для этого случая существует еще один специальный интерфейс под названием `AttributeTranslatorProviderInterface`, позволяющий валидатору извлекать переводы из реализующих его объектов.
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
