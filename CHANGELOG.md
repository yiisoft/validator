# Yii Validator Change Log

## 1.4.2 under development

- Enh #734: Add enum support (@samdark)

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
