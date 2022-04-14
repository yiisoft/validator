<?php
declare(strict_types=1);

namespace Yiisoft\Validator\Tests {
    require 'vendor/autoload.php';

    class FunctionExists
    {
        public static bool $isIdnFunctionExists = true;
    }
}

namespace Yiisoft\Validator\Rule\Url {

    use Yiisoft\Validator\Tests\FunctionExists;

    function function_exists(string $function): bool
    {
        if ($function === 'idn_to_ascii') {
            return FunctionExists::$isIdnFunctionExists;
        }
        return \function_exists($function);
    }
}
