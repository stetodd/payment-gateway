<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Request\Payment;

class RefundPaymentRequest
{
    public function __construct(
        public readonly string $paymentId,
        /** Minor units; null refunds whatever is left of the payment. */
        public readonly ?int $amount = null,
        /** @var array<string, string> carried onto the refund */
        public readonly array $metadata = [],
    ) {
        if ($amount !== null && $amount < 1) {
            throw new \InvalidArgumentException(sprintf('A refund amount must be at least 1, got %d.', $amount));
        }
    }
}
