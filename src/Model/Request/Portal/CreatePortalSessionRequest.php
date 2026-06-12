<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Request\Portal;

class CreatePortalSessionRequest
{
    public function __construct(
        public readonly string $customerId,
        public readonly string $returnUrl,
    ) {
    }
}
