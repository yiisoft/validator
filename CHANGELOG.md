# Yii Validator Change Log

## 2.0.0 under development

- New #633: Include labels in error messages and the ability to use labels with classes (@dood-)
- New #633: Add PHP attribute that sets property label for usage in error messages (@dood-)
- New #597, #608: Add debug collector for `yiisoft/yii-debug` (@xepozz, @vjik)
- New #610: Add `$escape` parameter to methods `Result::getAttributeErrorMessagesIndexedByPath()` and
  `Result::getErrorMessagesIndexedByPath()` that allow change or disable symbol which will be escaped in value path
  elements (@vjik)
- Bug #612: Disable escaping of asterisk char in value path returned by `Error::getValuePath(true)` (@vjik)
- New #617: Add `OneOf` rule (@arogachev)
- Chg #623: List translated attributes in error message for `OneOf` and `AtLeast` rules (@arogachev)
- Chg #624: Fix meaning of error message in `OneOf` rule (@arogachev)
- Chg #625: Improve meaning and use pluralization in error message for `OneOf` and `AtLeast` rules (@arogachev)
- Chg #626: Disallow `$min` greater than amount of `$attributes` in `AtLeast` configuration (@arogachev)
- Bug #632: Fix property name usage in error messages of rules in `Nested` rule (@vjik) 
- Enh #636: Improve psalm annotations in `Result` class (@vjik)
- Enh #637: Add German translation (@took)
- Chg #634: Move `getName()` method from `RuleInterface` to `RuleWithOptionsInterface` (@arogachev)
- Chg #634: Rename `RuleWithOptionsInterface` to `DumpedRuleInterface` (@arogachev)
- Chg #634: Use FQCN as a name for built-in rules during export with `RulesDumper` (@arogachev)
- Chg #634: Use FQCN as a name for rules not implementing `DumpedRuleInterface` during export with `RulesDumper`
- Enh #622: Use `json_validate()` built-in PHP function in `JsonHandler` if code is run with PHP 8.3 (@arogachev)
- Enh #639: Simplify validation of JSON in `JsonHandler` using built-in PHP functions for PHP versions below 8.3 
  (@arogachev)

## 1.1.0 April 06, 2023

- Enh #594: Add `StringValue` rule (@arogachev)
- Enh #567: Add immutable setter for modifying `Validator::$defaultSkipOnEmptyCondition` (@arogachev)
- Bug #586: Allow `0` as a limit in `CountableLimitTrait` (@arogachev)
- Bug #431: Add support for empty iterables to `WhenEmpty` (@arogachev)

## 1.0.0 February 22, 2023

- Initial release.
