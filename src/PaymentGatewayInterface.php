<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway;

use Stetodd\PaymentGateway\Model\Checkout\Session;
use Stetodd\PaymentGateway\Model\Customer;
use Stetodd\PaymentGateway\Model\Payment\Payment;
use Stetodd\PaymentGateway\Model\Request\Checkout\CreateCheckoutSessionRequest;
use Stetodd\PaymentGateway\Model\Request\Customer\CreateCustomerRequest;
use Stetodd\PaymentGateway\Model\Request\Payment\CancelPaymentRequest;
use Stetodd\PaymentGateway\Model\Request\Payment\CapturePaymentRequest;
use Stetodd\PaymentGateway\Model\Request\Payment\CreatePaymentHoldRequest;
use Stetodd\PaymentGateway\Model\Request\Payment\GetPaymentRequest;
use Stetodd\PaymentGateway\Model\Request\Portal\CreatePortalSessionRequest;
use Stetodd\PaymentGateway\Model\Request\Subscription\CancelSubscriptionRequest;
use Stetodd\PaymentGateway\Model\Request\Subscription\GetSubscriptionRequest;
use Stetodd\PaymentGateway\Model\Request\Subscription\ReactivateSubscriptionRequest;
use Stetodd\PaymentGateway\Model\Request\Subscription\UpdateSubscriptionPlanRequest;
use Stetodd\PaymentGateway\Model\Request\Subscription\UpdateSubscriptionQuantityRequest;
use Stetodd\PaymentGateway\Model\Subscription;

interface PaymentGatewayInterface
{
    public function createCheckoutSession(CreateCheckoutSessionRequest $request): Session;

    public function createCustomer(CreateCustomerRequest $request): Customer;

    public function cancelSubscription(CancelSubscriptionRequest $request): Subscription;

    public function reactivateSubscription(ReactivateSubscriptionRequest $request): void;

    public function updateSubscriptionPlan(UpdateSubscriptionPlanRequest $request): void;

    public function updateSubscriptionQuantity(UpdateSubscriptionQuantityRequest $request): void;

    public function createPortalSession(CreatePortalSessionRequest $request): string;

    public function getSubscription(GetSubscriptionRequest $request): Subscription;

    public function findSubscription(GetSubscriptionRequest $request): ?Subscription;

    /**
     * A hosted checkout that authorises a one-off amount without capturing it.
     * The completed-checkout webhook carries the payment id to capture or
     * cancel with.
     */
    public function createPaymentHoldSession(CreatePaymentHoldRequest $request): Session;

    public function capturePayment(CapturePaymentRequest $request): Payment;

    /** Releases an authorised hold (or cancels an unpaid payment). */
    public function cancelPayment(CancelPaymentRequest $request): Payment;

    /**
     * @throws \Stetodd\PaymentGateway\Exception\Payment\PaymentNotFoundException
     */
    public function getPayment(GetPaymentRequest $request): Payment;
}
