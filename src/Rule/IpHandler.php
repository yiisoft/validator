<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use InvalidArgumentException;
use RuntimeException;
use Yiisoft\NetworkUtilities\IpHelper;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * Checks if the value is a valid IPv4/IPv6 address or subnet.
 *
 * It also may change the value if normalization of IPv6 expansion is enabled.
 */
final class IpHandler implements RuleHandlerInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Negation char.
     *
     * Used to negate {@see $ranges} or {@see $network} or to negate validating value when {@see $allowNegation}
     * is used.
     */
    private const NEGATION_CHAR = '!';

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Ip) {
            throw new UnexpectedRuleException(Ip::class, $rule);
        }

        $this->checkAllowedVersions($rule);
        $result = new Result();
        $message = $this->translator->translate(
            $rule->getMessage(),
            ['attribute' => $context?->getAttribute(), 'value' => $value]
        );
        if (!is_string($value)) {
            $result->addError($message);
            return $result;
        }

        if (preg_match($rule->getIpParsePattern(), $value, $matches) === 0) {
            $result->addError($message);
            return $result;
        }
        $negation = !empty($matches['not'] ?? null);
        $ip = $matches['ip'];
        $cidr = $matches['cidr'] ?? null;
        $ipCidr = $matches['ipCidr'];

        try {
            $ipVersion = IpHelper::getIpVersion($ip, false);
        } catch (InvalidArgumentException $e) {
            $result->addError($message);
            return $result;
        }

        $result = $this->validateValueParts($rule, $result, $cidr, $negation, $value, $context);
        if (!$result->isValid()) {
            return $result;
        }
        $result = $this->validateVersion($rule, $result, $ipVersion, $value, $context);
        if (!$result->isValid()) {
            return $result;
        }
        return $this->validateCidr($rule, $result, $cidr, $ipCidr, $value, $context);
    }

    /**
     * Used to get the Regexp pattern for initial IP address parsing.
     */
    public function getIpParsePattern(): string
    {
        return '/^(?<not>' . preg_quote(
            self::NEGATION_CHAR,
            '/'
        ) . ')?(?<ipCidr>(?<ip>(?:' . IpHelper::IPV4_PATTERN . ')|(?:' . IpHelper::IPV6_PATTERN . '))(?:\/(?<cidr>-?\d+))?)$/';
    }

    private function checkAllowedVersions(Ip $rule): void
    {
        if (!$rule->isAllowIpv4() && !$rule->isAllowIpv6()) {
            throw new RuntimeException('Both IPv4 and IPv6 checks can not be disabled at the same time.');
        }
    }

    private function validateValueParts(
        Ip $rule,
        Result $result,
        ?string $cidr,
        bool $negation,
        mixed $value,
        ?ValidationContext $context
    ): Result {
        if ($cidr === null && $rule->isRequireSubnet()) {
            $message = $this->translator->translate(
                $rule->getNoSubnetMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($message);
            return $result;
        }
        if ($cidr !== null && !$rule->isAllowSubnet()) {
            $message = $this->translator->translate(
                $rule->getHasSubnetMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($message);
            return $result;
        }
        if ($negation && !$rule->isAllowNegation()) {
            $message = $this->translator->translate(
                $rule->getMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($message);
            return $result;
        }
        return $result;
    }

    private function validateVersion(
        Ip $rule,
        Result $result,
        int $ipVersion,
        mixed $value,
        ?ValidationContext $context
    ): Result {
        if ($ipVersion === IpHelper::IPV6 && !$rule->isAllowIpv6()) {
            $formattedMessage = $this->translator->translate(
                $rule->getIpv6NotAllowedMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
            return $result;
        }
        if ($ipVersion === IpHelper::IPV4 && !$rule->isAllowIpv4()) {
            $formattedMessage = $this->translator->translate(
                $rule->getIpv4NotAllowedMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
            return $result;
        }
        return $result;
    }

    private function validateCidr(
        Ip $rule,
        Result $result,
        ?string $cidr,
        string $ipCidr,
        mixed $value,
        ?ValidationContext $context
    ): Result {
        if ($cidr !== null) {
            try {
                IpHelper::getCidrBits($ipCidr);
            } catch (InvalidArgumentException $e) {
                $formattedMessage = $this->translator->translate(
                    $rule->getWrongCidrMessage(),
                    ['attribute' => $context?->getAttribute(), 'value' => $value]
                );
                $result->addError($formattedMessage);
                return $result;
            }
        }
        if (!$rule->isAllowed($ipCidr)) {
            $formattedMessage = $this->translator->translate(
                $rule->getNotInRangeMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
            return $result;
        }
        return $result;
    }
}
