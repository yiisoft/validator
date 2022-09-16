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
    public const IS_TRANSLATION_NEEDED = 'isTranslationNeeded';

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
        iterable|RulesProviderInterface|null $rules = null,
        ?ValidationContext $context = null,
    ): Result {
        $result = $this->decorated->validate($data, $rules, $context);

        $isTranslationNeeded = $context?->getParameter(self::IS_TRANSLATION_NEEDED, true) ?? true;
        $errorResult = new Result();
        foreach ($result->getErrors() as $error) {
            $errorResult->addError(
                $isTranslationNeeded
                    ? $this->translator->translate($error->getMessage(), $error->getParameters())
                    : $error->getMessage(),
                $error->getValuePath(),
                $error->getParameters(),
            );
        }

        return $errorResult;
    }
}
