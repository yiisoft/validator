# Internals

## Unit testing

The package is tested with [PHPUnit](https://phpunit.de/). To run tests:

```shell
./vendor/bin/phpunit
```

## Mutation testing

The package tests are checked with [Infection](https://infection.github.io/) mutation framework with
[Infection Static Analysis Plugin](https://github.com/Roave/infection-static-analysis-plugin). To run it:

```shell
./vendor/bin/roave-infection-static-analysis-plugin
```

## Static analysis

The code is statically analyzed with [Psalm](https://psalm.dev/). To run static analysis:

```shell
./vendor/bin/psalm
```

## Code style

Use [Rector](https://github.com/rectorphp/rector) to make codebase follow some specific rules or
use either newest or any specific version of PHP:

```shell
./vendor/bin/rector
```

## Dependencies

Use [ComposerRequireChecker](https://github.com/maglnet/ComposerRequireChecker) to detect transitive
[Composer](https://getcomposer.org/) dependencies.

## Translation

This package uses [po4a](https://github.com/mquinson/po4a) in Github Action for translations.  
Translation algorithm:
- Install an application for working with `.po` translation files. For example, [Poedit](https://poedit.net/), [Lokalize](https://apps.kde.org/ru/lokalize/), [Gtranslator](https://wiki.gnome.org/Apps/Gtranslator) or another.
- Find folder with the name of the file you want to translate in `/docs/po`
- Open the file with the `.po` extension in `Poedit` from the folder with the desired localization, for example `/docs/po/attribute-resolver-factory.md/ru/attribute-resolver-factory.md.ru.po`. If there is no localization yet, create an issue.
- Translate necessary strings and push the changes
- Open pull request to main repository

> Warning: Do not change the translation in files in `/docs/guide/{lang}` manually

If you have changed English documentation:
- Open pull request to main repository
- Pull updated branch after successful completion of workflow `Update docs translation` in Github Action
- Update translation in `.po` files by `Poedit`
- Push changes
