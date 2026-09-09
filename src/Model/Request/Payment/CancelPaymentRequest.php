<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Request\Payment;

class CancelPaymentRequest
{
    public function __construct(
        public readonly string $paymentId,
        public readonly ?string $reason = null,
    ) {
    }
}
