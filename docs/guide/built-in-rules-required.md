## `Required` - requiring values

Use `Yiisoft\Validator\Rule\Required` rule to make sure value is provided. What values are considered empty can be
customized via `$emptyCallback` option. Normalization is not performed here, so only a callable or special class is
needed. For more details see "Skipping empty values" section.

```php
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\EmptyCriteria\WhenNull;

new Required(emptyCallback: new WhenNull());
```
