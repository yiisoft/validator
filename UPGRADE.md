# Upgrading Instructions for Yii Validator

**!!!IMPORTANT!!!**

The following upgrading instructions are cumulative. That is,
if you want to upgrade from version A to version C and there is
version B between A and C, you need to follow the instructions
for both A and B.

## Upgrade from 1.x

* The signature for `Yiisoft\Validator\RuleHandlerIntarface::validate()` changed. If you have classes that implement 
  `RuleHandlerIntarface`, change the type of `$rule` parameter in method `validate()` from `object` to `RuleInterface`. 
  For example:

  ```php
  use Yiisoft\Validator\ValidationContext;
  
  public function validate(mixed $value, object $rule, ValidationContext $context): Result;
  ```
  
  Change to:

  ```php
  use Yiisoft\Validator\RuleInterface;
  use Yiisoft\Validator\ValidationContext;

  public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result;
  ```
  
* The type of `$escape` argument in `Yiisoft\Validator\Error::getValuePath()` changed from `bool|string|null` to 
  `string|null`. If you used `bool`, replace `false` with `null` and `true` with dot (`.`).

* For custom rules using `Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait`, apply the following changes for `$skipOnEmpty` 
  property in the constructor:

  - Turn in it into argument (remove `private` visibility).
  - Change type from `mixed` to more specific `bool|callable|null` 
  - Add manual initialization of property value.

  For example:

  ```php
  public function __construct(
      // ...
      private mixed $skipOnEmpty = null,
      // ...
  ) {
      // ...
  }
  ```
  
  Change to:

  ```php
  public function __construct(
      // ...
      bool|callable|null $skipOnEmpty = null,
      // ...
  ) {
      $this->skipOnEmpty = $this->skipOnEmpty;
  }
  ```
