<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Error;

/**
 * @internal
 */
final class ErrorMessagePostProcessor
{
    /**
     * @param TranslatorInterface $translator A translator instance used for translations of error messages.
     * @param string $translationCategory A translation category.
     */
    public function __construct(
        private TranslatorInterface $translator,
        private string $translationCategory,
    ) {
    }

    public function process(Error $error): string
    {
        if ($error->shouldTranslate()) {
            return $this->translator->translate(
                $error->getMessage(),
                $error->getParameters(),
                $this->translationCategory
            );
        }
        return $error->getMessage();
    }
}
