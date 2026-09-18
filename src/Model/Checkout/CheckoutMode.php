<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Checkout;

/**
 * What a checkout sets up: a subscription that bills again, or a single
 * payment that does not. The difference is the customer's contract, not a
 * detail of the vendor call — a one-off is never renewed, never cancelled and
 * never dunned, so the caller says which it wants.
 */
enum CheckoutMode: string
{
    case Subscription = 'subscription';
    case Payment = 'payment';
}
