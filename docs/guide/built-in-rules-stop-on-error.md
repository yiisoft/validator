# `StopOnError` - stop validation on first error

This rule applies to a group of rules and allows you to stop the validation for the whole group immediately after
an error occurs in any of the rules. This means that all rules following the rule that failed validation won't be
executed at all. 

This can be useful for performance-intensive validations, such as database queries or some complex calculations. 
The order of rules within a group is crucial here - the "lightweight" rules need to be placed above the "heavy" ones:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StopOnError;
use Yiisoft\Validator\Validator;

$data = 2;
$rule = new StopOnError([
    new HasLength(min: 3), // "Lightweight" rule, will be executed and won't pass the validation.
    new MyHeavyRule(), // // "Heavy" rule, won't be executed at all because of the existing error.
]);
$result = (new Validator())->validate($data, $rule);
```
