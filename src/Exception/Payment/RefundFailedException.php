<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Exception\Payment;

/**
 * The vendor refused to refund: more than is left on the payment, a payment
 * that was never captured, a charge under dispute.
 */
class RefundFailedException extends \RuntimeException
{
    public function __construct(
        public readonly string $paymentId,
        string $reason,
        ?\Throwable $previous = null,
    ) {
        parent::__construct(sprintf('Payment "%s" could not be refunded: %s', $paymentId, $reason), 0, $previous);
    }
}
