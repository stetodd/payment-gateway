<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Request\Payment;

class CapturePaymentRequest
{
    public function __construct(
        public readonly string $paymentId,
        /** Minor units; null captures the full authorised amount. */
        public readonly ?int $amountToCapture = null,
    ) {
    }
}
