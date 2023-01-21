# `StopOnError` - validation until the first error

This rule applies for a group of rules and allows to stop the validation for the whole group right after the error has
occurred in any of rules. This means that all rules following the one that did not pass the validation won't be run at 
all. 

It can be helpful when performance heavy checks involved - for example, database queries or some complex calculations. 
The order of rules within a group is crucial here - the "lightweight" rules need to be placed above the "heavy" ones:

```php
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\StopOnError;
use Yiisoft\Validator\Validator;

$data = 2;
$rule = new StopOnError([
    new HasLength(min: 3), // "Lightweight" rule, will be run and won't pass the validation.
    new MyHeavyRule(), // // "Heavy" rule, won't be run at all because of already existing error.
]);
$result = (new Validator())->validate($data, $rule);
```
