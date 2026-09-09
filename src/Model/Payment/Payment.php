<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Payment;

/**
 * A one-off payment as the vendor holds it: its id, status, and the amount in
 * the currency's minor unit (pence, cents).
 */
class Payment
{
    public function __construct(
        public readonly string $id,
        public readonly PaymentStatus $status,
        public readonly int $amount,
        public readonly string $currency,
        public readonly int $amountCaptured = 0,
    ) {
    }
}
