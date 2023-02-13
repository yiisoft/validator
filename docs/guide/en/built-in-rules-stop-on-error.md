# `StopOnError` - stop validation on the first error

This rule applies to a group of rules and allows you to stop the validation for the whole group immediately after
an error occurs in any of the rules. This means that all rules following the rule that failed validation won't be
run at all. 

This can be useful for performance-intensive validations, such as database queries or some complex calculations. 
The order of rules within a group is crucial here - the "lightweight" rules need to be placed above the "heavy" ones:

```php
use Yiisoft\Validator\Rule\Length;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StopOnError;
use Yiisoft\Validator\Validator;

$data = 2;
$rule = new StopOnError([
    new Length(min: 3), // "Lightweight" rule, will be run and won't pass the validation.
    new MyHeavyRule(), // "Heavy" rule, won't be run at all because of the existing error.
]);
$result = (new Validator())->validate($data, $rule);
```
