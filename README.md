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
[![Mutation testing badge](https://img.shields.io/endpoint?style=flat&url=https%3A%2F%2Fbadge-api.stryker-mutator.io%2Fgithub.com%2Fyiisoft%2Fwidget%2Fmaster)](https://dashboard.stryker-mutator.io/reports/github.com/yiisoft/validator/master)
[![type-coverage](https://shepherd.dev/github/yiisoft/validator/coverage.svg)](https://shepherd.dev/github/yiisoft/validator)
[![static analysis](https://github.com/yiisoft/validator/workflows/static%20analysis/badge.svg)](https://github.com/yiisoft/validator/actions?query=workflow%3A%22static+analysis%22)
[![psalm-level](https://shepherd.dev/github/yiisoft/validator/level.svg)](https://shepherd.dev/github/yiisoft/validator)

The package provides data validation capabilities.

## Features

- Could be used with any object.
- Supports PHP 8 attributes.
- Skip further validation if an error occurred for the same field.
- Skip validation of empty value.
- Error message formatting.
- Conditional validation.
- Could pass context to validation rule.
- Common rules bundled.

## Requirements

- PHP 8.0 or higher.
- `JSON` PHP extension.
- `mbstring` PHP extension.

## Installation

The package could be installed with composer:

```shell
composer require yiisoft/validator
```

## Testing

### Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

### Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework with
[Infection Static Analysis Plugin](https://github.com/Roave/infection-static-analysis-plugin). To run it:

```shell
./vendor/bin/roave-infection-static-analysis-plugin
```

### Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

### Code style

Use [Rector](https://github.com/rectorphp/rector) to make codebase follow some specific rules or
use either newest or any specific version of PHP:

```shell
./vendor/bin/rector
```

### Dependencies

Use [ComposerRequireChecker](https://github.com/maglnet/ComposerRequireChecker) to detect transitive
[Composer](https://getcomposer.org/) dependencies.

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
