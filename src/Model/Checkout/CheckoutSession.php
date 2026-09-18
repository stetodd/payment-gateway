<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Checkout;

/**
 * A checkout session read back from the vendor, so a caller can find out how a
 * checkout ended without waiting for a webhook to tell it.
 */
class CheckoutSession
{
    /** @param array<string, string> $metadata */
    public function __construct(
        public readonly string $id,
        public readonly CheckoutStatus $status,
        /** Settled: paid, or completed with nothing to pay. */
        public readonly bool $paid,
        /** Set when the checkout set up a subscription. */
        public readonly ?string $subscriptionId = null,
        public readonly ?string $customerId = null,
        public readonly int $amountTotal = 0,
        public readonly array $metadata = [],
        /** Set when the checkout took a single payment: what to refund against. */
        public readonly ?string $paymentIntentId = null,
    ) {
    }
}
