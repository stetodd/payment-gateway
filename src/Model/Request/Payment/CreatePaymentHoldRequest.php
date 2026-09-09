<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Request\Payment;

use Stetodd\PaymentGateway\Model\Customer;

/**
 * Asks for a hosted checkout that authorises a one-off amount without
 * capturing it — a hold the caller captures on delivery or cancels. The
 * amount is in the currency's minor unit.
 */
class CreatePaymentHoldRequest
{
    public function __construct(
        public readonly Customer $customer,
        public readonly int $amount,
        public readonly string $currency,
        /** What the customer sees on the checkout and their statement. */
        public readonly string $description,
        /** Absolute URL the vendor sends the customer to after a completed checkout. */
        public readonly string $successUrl,
        /** Absolute URL the vendor sends the customer to when they abandon checkout. */
        public readonly string $cancelUrl,
        /** @var array<string, string> carried onto the checkout and the payment */
        public readonly array $metadata = [],
    ) {
    }

    /**
     * @return array<string, string>
     */
    public function getMetadata(): array
    {
        return array_merge($this->metadata, $this->customer->metadata);
    }
}
