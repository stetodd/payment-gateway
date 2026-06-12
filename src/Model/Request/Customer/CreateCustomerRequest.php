<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Request\Customer;

class CreateCustomerRequest
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
    ) {
    }
}
