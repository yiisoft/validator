# Yii Validator Change Log

## 2.0.0 under development

- New #597, #608: Add debug collector for `yiisoft/yii-debug` (@xepozz, @vjik)
- New #610: Add `$escape` parameter to methods `Result::getAttributeErrorMessagesIndexedByPath()` and
  `Result::getErrorMessagesIndexedByPath()` that allow change or disable symbol which will be escaped in value path
  elements (@vjik)
- Bug #612: Disable escaping of asterisk char in value path returned by `Error::getValuePath(true)` (@vjik)
- New #617: Add `OneOf` rule (@arogachev)
- Chg #623: List translated attributes in error message for `OneOf` and `AtLeast` rules (@arogachev)
- Chg #624: Fix meaning of error message in `OneOf` rule (@arogachev)
- Chg #625: Improve meaning and use pluralization in error message for `OneOf` and `AtLeast` rules (@arogachev)

## 1.1.0 April 06, 2023

- Enh #594: Add `StringValue` rule (@arogachev)
- Enh #567: Add immutable setter for modifying `Validator::$defaultSkipOnEmptyCondition` (@arogachev)
- Bug #586: Allow `0` as a limit in `CountableLimitTrait` (@arogachev)
- Bug #431: Add support for empty iterables to `WhenEmpty` (@arogachev)

## 1.0.0 February 22, 2023

- Initial release.
