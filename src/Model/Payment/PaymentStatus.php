<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Model\Payment;

/**
 * The lifecycle of a one-off payment. RequiresCapture is the authorised hold:
 * funds reserved on the customer's payment method, captured or released later.
 */
enum PaymentStatus: string
{
    case RequiresPaymentMethod = 'requires_payment_method';
    case RequiresConfirmation = 'requires_confirmation';
    case RequiresAction = 'requires_action';
    case Processing = 'processing';
    case RequiresCapture = 'requires_capture';
    case Succeeded = 'succeeded';
    case Cancelled = 'cancelled';

    public function isHeld(): bool
    {
        return $this === self::RequiresCapture;
    }

    public function isCaptured(): bool
    {
        return $this === self::Succeeded;
    }

    public function isFinal(): bool
    {
        return $this === self::Succeeded || $this === self::Cancelled;
    }
}
