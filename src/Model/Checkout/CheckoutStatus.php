<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Checkout;

enum CheckoutStatus: string
{
    /** Still payable: the customer has not finished, and the session has not timed out. */
    case Open = 'open';

    /** The customer got to the end. Whether money moved is {@see CheckoutSession::$paid}. */
    case Complete = 'complete';

    /** Timed out unpaid, and can never be paid now. */
    case Expired = 'expired';
}
