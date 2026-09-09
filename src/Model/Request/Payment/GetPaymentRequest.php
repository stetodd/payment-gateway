<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Request\Payment;

class GetPaymentRequest
{
    public function __construct(
        public readonly string $paymentId,
    ) {
    }
}
