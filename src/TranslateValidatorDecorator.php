<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

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
     * @param iterable|RulesProviderInterface|null $rules
     * @param ValidationContext|null $context
     *
     * @return Result
     */
    public function validate(
        mixed $data,
        iterable|RulesProviderInterface|null $rules = null,
        ?ValidationContext $context = null,
    ): Result {
        $context = new ValidationContext(
            $context?->getValidator() ?? $this,
            $context?->getDataSet() ?? null,
            null,
            $context?->getParameters() ?? []
        );
        $result = $this->decorated->validate($data, $rules, $context);

        if (!($context->getParameter(self::IS_TRANSLATION_NEEDED, true))) {
            return $result;
        }

        $errorResult = new Result();
        foreach ($result->getErrors() as $error) {
            $errorResult->addError(
                $this->translator->translate($error->getMessage()->getMessage(), $error->getParameters()),
                $error->getValuePath(),
                $error->getParameters(),
            );
        }

        return $errorResult;
    }
}
