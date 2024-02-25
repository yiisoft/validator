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
        switch ($error->getMessageProcessing()) {
            case Error::MESSAGE_TRANSLATE:
                return $this->translator->translate(
                    $error->getMessage(),
                    $error->getParameters(),
                    $this->translationCategory
                );

            case Error::MESSAGE_FORMAT:
                return $this->messageFormatter->format(
                    $error->getMessage(),
                    $error->getParameters(),
                    $this->messageFormatterLocale,
                );

            case Error::MESSAGE_NONE:
            default:
                return $error->getMessage();
        }
    }
}
