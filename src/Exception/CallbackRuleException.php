<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Exception;

use Yiisoft\FriendlyException\FriendlyExceptionInterface;

class CallbackRuleException extends \Exception implements FriendlyExceptionInterface
{
    public function getName(): string
    {
        return 'Invalid callable returned value';
    }

    public function getSolution(): ?string
    {
        return <<<SOLUTION
            The callback must return an instance of \Yiisoft\Validator\Result. An example of a valid callback:
                static function (): \Yiisoft\Validator\Result
                {
                    \$result = new \Yiisoft\Validator\Result();

                    ...

                    return \$result;
                }
        SOLUTION;
    }
}
