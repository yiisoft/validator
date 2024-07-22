# Yii Validator Change Log

## 2.0.0 under development

- New #630: Include attribute name in error messages when it's present (@dood-)
- New #646, #653: Add `DateTime` rule (@pamparam83)
- New #615: Add the `Each::PARAMETER_EACH_KEY` validation context parameter that available during `Each` rule handling
  and containing the current key (@dood-)
- Enh #648: Raise the minimum version of PHP to 8.1 (@pamparam83)
- New #633: Add PHP attribute that sets property label for usage in error messages (@dood-)
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
- Chg #679: Change type of `$rule` argument in `RuleHandlerInterface::validate()` from `object` to `RuleInterface`
  (@arogachev)
- Chg #660: Change type of `$skipOnEmpty` argument in rules' constructors from `mixed` to `bool|callable|null`
- Chg #613: Change type of `$escape` argument in `Error::getValuePath()` from `bool|string|null` to `string|null`
  (@arogachev)
- Enh #726: Refactor `Result::add()`: took `array_merge()` out of the `foreach` (@lav45)

## 1.4.1 June 11, 2024

- Bug #719: Fix parameters leak in context validation (@vjik)

## 1.4.0 May 22, 2024

- New #649: Add `getFirstErrorMessagesIndexedByPath()` and `getFirstErrorMessagesIndexedByAttribute()` methods to
  `Result` (@arogachev)
- New #655: Add rules for validating value types - `boolean`, `float`, `integer`, `string` (@arogachev)
- New #657: Add `Result::add()` method for merging other results to the base one (@arogachev)
- New #687: Add `UniqueIterable` rule (@arogachev)
- New #693: Add `AnyRule` rule (@arogachev)

## 1.3.0 April 04, 2024

- New #665: Add methods `addErrorWithFormatOnly()` and `addErrorWithoutPostProcessing()` to `Result` object (@vjik)
- New #670, #677, #680: Add `Image` validation rule (@vjik, @arogachev)
- New #678: Add `Date`, `DateTime` and `Time` validation rules (@vjik)
- Enh #668: Clarify psalm types in `Result` (@vjik)

## 1.2.0 February 21, 2024

- New #597, #608: Add debug collector for `yiisoft/yii-debug` (@xepozz, @vjik)
- New #610: Add `$escape` parameter to methods `Result::getAttributeErrorMessagesIndexedByPath()` and
  `Result::getErrorMessagesIndexedByPath()` that allow change or disable symbol which will be escaped in value path
  elements (@vjik)
- New #617: Add `OneOf` rule (@arogachev)
- Enh #658: Minor refactoring of `EmailHandler::validate()` method (@vjik)
- Enh #658: Add more specific psalm type for "skip on empty" callable (@vjik)
- Enh #658: Make `$isAttributeMissing` parameter of empty conditions (`NeverEmpty`, `WhenEmpty`, `WhenMissing`,
  `WhenNull`) optional (@vjik)
- Bug #612: Disable escaping of asterisk char in value path returned by `Error::getValuePath(true)` (@vjik)

## 1.1.0 April 06, 2023

- Enh #594: Add `StringValue` rule (@arogachev)
- Enh #567: Add immutable setter for modifying `Validator::$defaultSkipOnEmptyCondition` (@arogachev)
- Bug #586: Allow `0` as a limit in `CountableLimitTrait` (@arogachev)
- Bug #431: Add support for empty iterables to `WhenEmpty` (@arogachev)

## 1.0.0 February 22, 2023

- Initial release.
