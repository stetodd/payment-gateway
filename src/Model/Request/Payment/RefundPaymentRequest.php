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
        /**
         * Names the one refund this request may ever make. A request repeated
         * with the same key returns the refund already made against the payment
         * instead of paying again, whatever amount the repeat asks for.
         */
        public readonly ?string $idempotencyKey = null,
    ) {
        if ($amount !== null && $amount < 1) {
            throw new \InvalidArgumentException(sprintf('A refund amount must be at least 1, got %d.', $amount));
        }
        if ($idempotencyKey === '') {
            throw new \InvalidArgumentException('An idempotency key cannot be empty.');
        }
    }
}
