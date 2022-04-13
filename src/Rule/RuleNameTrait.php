<?php
declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

trait RuleNameTrait
{
    /**
     * Get name of the rule to be used when rule is converted to array.
     * By default, it returns base name of the class, first letter in lowercase.
     */
    public function getName(): string
    {
        $className = static::class;
        return lcfirst(substr($className, strrpos($className, '\\') + 1));
    }

}
