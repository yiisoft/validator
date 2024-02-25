<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

use Yiisoft\Translator\MessageFormatterInterface;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Error;

/**
 * @internal
 */
final class MessageProcessor
{
    /**
     * @param TranslatorInterface $translator A translator instance used for translations of error messages.
     * @param string $translationCategory A translation category.
     * @param MessageFormatterInterface $messageFormatter A message formatter instance used for formats of error
     * messages.
     * @param string $messageFormatterLocale Locale to use when error message requires format only.
     */
    public function __construct(
        private TranslatorInterface $translator,
        private string $translationCategory,
        private MessageFormatterInterface $messageFormatter,
        private string $messageFormatterLocale,
    ) {
    }

    public function process(Error $error): string
    {
        return match ($error->getMessageProcessing()) {
            Error::MESSAGE_TRANSLATE => $this->translator->translate(
                $error->getMessage(),
                $error->getParameters(),
                $this->translationCategory
            ),
            Error::MESSAGE_FORMAT => $this->messageFormatter->format(
                $error->getMessage(),
                $error->getParameters(),
                $this->messageFormatterLocale,
            ),
            default => $error->getMessage(),
        };
    }
}
