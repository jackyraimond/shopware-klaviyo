<?php declare(strict_types=1);

namespace Klaviyo\Integration\System\Tracking;

use Klaviyo\Integration\Klaviyo\Gateway\Result\OrderTrackingResult;
use Klaviyo\Integration\System\Tracking\Event\Cart\CartEventRequestBag;
use Klaviyo\Integration\System\Tracking\Event\Order\OrderTrackingEventsBag;
use Shopware\Core\Framework\Context;

interface EventsTrackerInterface
{
    public const SUBSCRIBER_EVENT_SUB = 'od-klaviyo-subscriber-subscribed';
    public const SUBSCRIBER_EVENT_UNSUB = 'od-klaviyo-subscriber-unsubscribed';

    public const SUBSCRIBER_EVENTS = [
        self::SUBSCRIBER_EVENT_SUB,
        self::SUBSCRIBER_EVENT_UNSUB,
    ];

    public const ORDER_EVENT_PLACED = 'od-klaviyo-order-placed';
    public const ORDER_EVENT_CANCELED = 'od-klaviyo-order-canceled';
    public const ORDER_EVENT_REFUNDED = 'od-klaviyo-order-refunded';
    public const ORDER_EVENT_FULFILLED = 'od-klaviyo-order-fulfilled';

    public const ORDER_EVENTS = [
        self::ORDER_EVENT_PLACED,
        self::ORDER_EVENT_CANCELED,
        self::ORDER_EVENT_REFUNDED,
        self::ORDER_EVENT_FULFILLED,
    ];

    public function trackPlacedOrders(Context $context, OrderTrackingEventsBag $trackingBag): OrderTrackingResult;

    public function trackFulfilledOrders(Context $context, OrderTrackingEventsBag $trackingBag): OrderTrackingResult;

    public function trackCanceledOrders(Context $context, OrderTrackingEventsBag $trackingBag): OrderTrackingResult;

    public function trackRefundOrders(Context $context, OrderTrackingEventsBag $trackingBag): OrderTrackingResult;

    public function trackAddedToCart(Context $context, CartEventRequestBag $requestBag);
}
