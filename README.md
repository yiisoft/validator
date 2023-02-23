<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://yiisoft.github.io/docs/images/yii_logo.svg" height="100px">
    </a>
    <h1 align="center">Yii Validator</h1>
    <br>
</p>

[![Latest Stable Version](https://poser.pugx.org/yiisoft/validator/v/stable.png)](https://packagist.org/packages/yiisoft/validator)
[![Total Downloads](https://poser.pugx.org/yiisoft/validator/downloads.png)](https://packagist.org/packages/yiisoft/validator)
[![Build status](https://github.com/yiisoft/validator/workflows/build/badge.svg)](https://github.com/yiisoft/validator/actions?query=workflow%3Abuild)
[![Code Coverage](https://codecov.io/gh/yiisoft/validator/branch/master/graph/badge.svg)](https://codecov.io/gh/yiisoft/validator)
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fvalidator%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/validator/master)
[![type-coverage](https://shepherd.dev/github/yiisoft/validator/coverage.svg)](https://shepherd.dev/github/yiisoft/validator)
[![static analysis](https://github.com/yiisoft/validator/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/validator/actions?query=workflow%3A%22static+analysis%22)
[![psalm-level](https://shepherd.dev/github/yiisoft/validator/level.svg)](https://shepherd.dev/github/yiisoft/validator)

This package provides data validation capabilities.

## Features

- Validates any data: arrays, objects, scalar values, etc.
- Supports custom data sets.
- Handles nested data structures (one-to-one and one-to-many).
- Supports PHP 8 attributes.
- Error message formatting and translation.
- Attribute names translation.
- Conditional validation:
  - Skip validation of "empty" value with possibility to configure "empty" condition.
  - Skip further validation if an error occurred for the same attribute.
  - Skip validation depending on a condition.
- Possibility to use context in rule handler.
- Common rules bundled.
- Supports DI container for creating custom rule handlers with extra dependencies.
- Exporting rules options for using in the frontend.

## Requirements

- PHP 8.0 or higher.
- `mbstring` PHP extension.

## Installation

The package could be installed with composer:

```shell
composer require yiisoft/validator
```

## General usage

Validator allows to check data in any format. For example, when data is an object:

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
        public ?string $name = null,

        #[Number(min: 21)]
        public ?int $age = null,

        #[Email]
        public ?string $email = null,

        public ?string $phone = null,
    ) {
    }
}

$person = new Person(
    name: 'John', 
    age: 17, 
    email: 'john@example.com',
    phone: null
);

$result = (new Validator())->validate($person);
```

The validation result is an object that allows to check whether validation was successful:

```php
$result->isValid();
```

It also contains errors occurred during validation:

```php
$result->getErrorMessages();
```

## Documentation

- [Guide](docs/guide/en/README.md)
- [Internals](docs/internals.md)

## License

The Yii Validator is free software. It is released under the terms of the BSD License.
Please see [`LICENSE`](./LICENSE.md) for more information.

Maintained by [Yii Software](https://www.yiiframework.com/).

## Support the project

[![Open Collective](https://img.shields.io/badge/Open%20Collective-sponsor-7eadf1?logo=open%20collective&logoColor=7eadf1&labelColor=555555)](https://opencollective.com/yiisoft)

## Follow updates

[![Official website](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
[![Twitter](https://img.shields.io/badge/twitter-follow-1DA1F2?logo=twitter&logoColor=1DA1F2&labelColor=555555?style=flat)](https://twitter.com/yiiframework)
[![Telegram](https://img.shields.io/badge/telegram-join-1DA1F2?style=flat&logo=telegram)](https://t.me/yii3en)
[![Facebook](https://img.shields.io/badge/facebook-join-1DA1F2?style=flat&logo=facebook&logoColor=ffffff)](https://www.facebook.com/groups/yiitalk)
[![Slack](https://img.shields.io/badge/slack-join-1DA1F2?style=flat&logo=slack)](https://yiiframework.com/go/slack)
