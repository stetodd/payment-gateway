<?php

declare(strict_types=1);

namespace Stetodd\PaymentGateway\Testing;

use Stetodd\PaymentGateway\Exception\Payment\PaymentNotFoundException;
use Stetodd\PaymentGateway\Model\Checkout\Session;
use Stetodd\PaymentGateway\Model\Customer;
use Stetodd\PaymentGateway\Model\Payment\Payment;
use Stetodd\PaymentGateway\Model\Payment\PaymentStatus;
use Stetodd\PaymentGateway\Model\Portal\PortalSession;
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
use Stetodd\PaymentGateway\PaymentGatewayInterface;

class SimulatorPaymentGateway implements PaymentGatewayInterface
{
    /** @var array<string, array<array-key, object>> */
    private array $responseQueue = [
        'checkout_session' => [],
        'create_customer' => [],
        'get_subscription' => [],
        'cancel_subscription' => [],
        'create_portal_session' => [],
        'payment_hold_session' => [],
    ];

    /** @var array<string, array<array-key, object>> */
    private array $responses = [
        'checkout_session' => [],
        'create_customer' => [],
        'get_subscription' => [],
        'cancel_subscription' => [],
        'create_portal_session' => [],
        'payment_hold_session' => [],
    ];

    /**
     * One-off payments the simulator knows about, keyed by id. A hold session
     * does not create one (the customer has not checked out yet); tests
     * register the authorised payment with holdPayment() as the webhook would.
     *
     * @var array<string, Payment>
     */
    private array $payments = [];

    /** @var list<CreatePaymentHoldRequest> */
    public array $paymentHoldRequests = [];

    /** @var array<string, int> */
    private array $callCounts = [
        'reactivate_subscription' => 0,
        'update_subscription_plan' => 0,
        'update_subscription_quantity' => 0,
    ];

    public function willReturnResponse(string $key, object $response): void
    {
        if (array_key_exists($key, $this->responseQueue) === false) {
            throw new \InvalidArgumentException(sprintf('Response key "%s" not found', $key));
        }

        $typeCheck = match ($key) {
            'checkout_session' => $response instanceof Session,
            'create_customer' => $response instanceof Customer,
            'get_subscription' => $response instanceof Subscription,
            'cancel_subscription' => $response instanceof Subscription,
            'create_portal_session' => $response instanceof PortalSession,
            'payment_hold_session' => $response instanceof Session,
        };

        if ($typeCheck === false) {
            throw new \InvalidArgumentException('Invalid response type.');
        }

        $this->responseQueue[$key][] = $response;
    }

    public function createCheckoutSession(CreateCheckoutSessionRequest $request): Session
    {
        /** @var Session $response */
        $response = $this->getResponse('checkout_session');

        return $response;
    }

    public function createCustomer(CreateCustomerRequest $request): Customer
    {
        /** @var Customer $response */
        $response = $this->getResponse('create_customer');

        return $response;
    }

    public function cancelSubscription(CancelSubscriptionRequest $request): Subscription
    {
        /** @var Subscription $response */
        $response = $this->getResponse('cancel_subscription');

        return $response;
    }

    public function reactivateSubscription(ReactivateSubscriptionRequest $request): void
    {
        ++$this->callCounts['reactivate_subscription'];
    }

    public function updateSubscriptionPlan(UpdateSubscriptionPlanRequest $request): void
    {
        ++$this->callCounts['update_subscription_plan'];
    }

    public function updateSubscriptionQuantity(UpdateSubscriptionQuantityRequest $request): void
    {
        ++$this->callCounts['update_subscription_quantity'];
    }

    public function createPortalSession(CreatePortalSessionRequest $request): string
    {
        /** @var PortalSession $response */
        $response = $this->getResponse('create_portal_session');

        return $response->url;
    }

    public function getSubscription(GetSubscriptionRequest $request): Subscription
    {
        /** @var Subscription $response */
        $response = $this->getResponse('get_subscription');

        return $response;
    }

    public function findSubscription(GetSubscriptionRequest $request): ?Subscription
    {
        return $this->getSubscription($request);
    }

    /** Registers an authorised (uncaptured) payment, as a completed hold checkout would. */
    public function holdPayment(string $paymentId, int $amount, string $currency = 'gbp'): void
    {
        $this->payments[$paymentId] = new Payment($paymentId, PaymentStatus::RequiresCapture, $amount, $currency);
    }

    public function createPaymentHoldSession(CreatePaymentHoldRequest $request): Session
    {
        $this->paymentHoldRequests[] = $request;

        /** @var Session $response */
        $response = $this->getResponse('payment_hold_session');

        return $response;
    }

    public function capturePayment(CapturePaymentRequest $request): Payment
    {
        $payment = $this->getPayment(new GetPaymentRequest($request->paymentId));
        if (!$payment->status->isHeld()) {
            throw new \RuntimeException(sprintf('Payment "%s" is not held (%s).', $payment->id, $payment->status->value));
        }
        $captured = $request->amountToCapture ?? $payment->amount;

        return $this->payments[$payment->id] = new Payment($payment->id, PaymentStatus::Succeeded, $payment->amount, $payment->currency, $captured);
    }

    public function cancelPayment(CancelPaymentRequest $request): Payment
    {
        $payment = $this->getPayment(new GetPaymentRequest($request->paymentId));
        if ($payment->status->isFinal()) {
            throw new \RuntimeException(sprintf('Payment "%s" is already %s.', $payment->id, $payment->status->value));
        }

        return $this->payments[$payment->id] = new Payment($payment->id, PaymentStatus::Cancelled, $payment->amount, $payment->currency);
    }

    public function getPayment(GetPaymentRequest $request): Payment
    {
        return $this->payments[$request->paymentId]
            ?? throw new PaymentNotFoundException($request->paymentId);
    }

    private function getResponse(string $key): object
    {
        if (count($this->responseQueue[$key]) === 0) {
            throw new \RuntimeException(sprintf('No response set for %s', $key));
        }

        $response = array_pop($this->responseQueue[$key]);

        $this->responses[$key][] = $response;

        return $response;
    }

    public function getResponseCount(string $key): int
    {
        return count($this->responses[$key]);
    }

    public function getCallCount(string $key): int
    {
        if (!array_key_exists($key, $this->callCounts)) {
            throw new \InvalidArgumentException(sprintf('Call count key "%s" not found', $key));
        }

        return $this->callCounts[$key];
    }
}
