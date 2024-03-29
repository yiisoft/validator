# Yii Validator Change Log

## 1.3.0 under development

- New #665: Add methods `addErrorWithFormatOnly()` and `addErrorWithoutPostProcessing()` to `Result` object (@vjik)
- Enh #668: Clarify psalm types in `Result` (@vjik)
- New #670: Add `Image` validation rule (@vjik, @arogachev)
- New #678: Add `Date` validation rule (@vjik)

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
