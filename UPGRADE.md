# Upgrading Instructions for Yii Validator

### !!!IMPORTANT!!!

The following upgrading instructions are cumulative. That is,
if you want to upgrade from version A to version C and there is
version B between A and C, you need to follow the instructions
for both A and B.

## Upgrade from 1.x

* The signature for `Yiisoft\Validator'RuleHandlerIntarface::validate()` changed from:

  ```php
  use Yiisoft\Validator\ValidationContext;
  
  public function validate(mixed $value, object $rule, ValidationContext $context): Result;
  ```
  
  to:

  ```php
  use Yiisoft\Validator\RuleInterface;
  use Yiisoft\Validator\ValidationContext;

  public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result;
  ```
