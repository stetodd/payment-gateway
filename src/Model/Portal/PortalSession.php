<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Portal;

class PortalSession
{
    public function __construct(public readonly string $url)
    {
    }
}
