<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Closure;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Yiisoft\Translator\TranslatorInterface;

/**
 * Validator validates {@link DataSetInterface} against rules set for data set attributes.
 */
final class TranslateValidatorDecorator implements ValidatorInterface
{
    public function __construct(
        private ValidatorInterface $decorated,
        private TranslatorInterface $translator,
    ) {
    }

    /**
     * @param DataSetInterface|mixed|RulesProviderInterface $data
     * @param class-string|iterable<Closure|Closure[]|RuleInterface|RuleInterface[]>|RulesProviderInterface|null $rules
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function validate(
        mixed $data,
        iterable|RulesProviderInterface|null $rules = null
    ): Result {
        $result = $this->decorated->validate($data, $rules);

        $errorResult = new Result();
        foreach ($result->getErrors() as $error) {
            $errorResult->addError(
                $this->translator->translate($error->getMessage(), $error->getParameters()),
                $error->getValuePath(),
                $error->getParameters(),
            );
        }

        return $errorResult;
    }
}
