<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Checkout;

class Session
{
    public function __construct(public readonly string $url)
    {
    }
}
