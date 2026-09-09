<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Exception\Payment;

class PaymentNotFoundException extends \RuntimeException
{
    public function __construct(string $paymentId, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('Payment "%s" was not found.', $paymentId), 0, $previous);
    }
}
