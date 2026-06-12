<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Request\Checkout;

class GetCustomerRequest
{
    public function __construct(
        public readonly string $id,
    ) {
    }
}
