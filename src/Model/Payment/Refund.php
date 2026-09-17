<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Payment;

/**
 * Money returned against a payment, in the currency's minor unit.
 */
class Refund
{
    public function __construct(
        public readonly string $id,
        public readonly string $paymentId,
        public readonly RefundStatus $status,
        public readonly int $amount,
        public readonly string $currency,
        /** The vendor's reason when the refund failed. */
        public readonly ?string $failureReason = null,
    ) {
    }
}
