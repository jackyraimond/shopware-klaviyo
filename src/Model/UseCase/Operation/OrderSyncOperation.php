<?php declare(strict_types=1);

namespace Klaviyo\Integration\Model\UseCase\Operation;

use Klaviyo\Integration\Async\Message\OrderSyncMessage;
use Klaviyo\Integration\Klaviyo\Gateway\Result\OrderTrackingResult;
use Klaviyo\Integration\System\Tracking\Event\Order\OrderEvent;
use Klaviyo\Integration\System\Tracking\Event\Order\OrderTrackingEventsBag;
use Klaviyo\Integration\System\Tracking\EventsTrackerInterface as Tracker;
use Od\Scheduler\Model\Job\JobHandlerInterface;
use Od\Scheduler\Model\Job\JobResult;
use Shopware\Core\Checkout\Order\Aggregate\OrderTransaction\OrderTransactionStates;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Checkout\Order\OrderStates;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;

class OrderSyncOperation implements JobHandlerInterface
{
    public const OPERATION_HANDLER_CODE = 'od-klaviyo-order-sync-handler';

    private EntityRepositoryInterface $orderRepository;
    private Tracker $eventsTracker;

    public function __construct(
        EntityRepositoryInterface $orderRepository,
        Tracker $eventsTracker
    ) {
        $this->orderRepository = $orderRepository;
        $this->eventsTracker = $eventsTracker;
    }

    /**
     * @param OrderSyncMessage $message
     * @return JobResult
     */
    public function execute(object $message): JobResult
    {
        $result = new JobResult();
        $context = Context::createDefaultContext();
        $eventsBags = [
            Tracker::ORDER_EVENT_PLACED => new OrderTrackingEventsBag(),
            Tracker::ORDER_EVENT_REFUNDED => new OrderTrackingEventsBag(),
            Tracker::ORDER_EVENT_CANCELED => new OrderTrackingEventsBag(),
            Tracker::ORDER_EVENT_FULFILLED => new OrderTrackingEventsBag(),
        ];
        $orderCriteria = new Criteria();

        $orderCriteria->addFilter(new EqualsAnyFilter('id', $message->getOrderIds()));
        $orderCriteria->addAssociation('stateMachineState');
        $orderCriteria->addAssociation('lineItems.product');
        $orderCriteria->addAssociation('orderCustomer.customer.defaultBillingAddress');
        $orderCriteria->addAssociation('orderCustomer.customer.defaultShippingAddress');
        $orderCollection = $this->orderRepository->search($orderCriteria, $context);

        /** @var OrderEntity $order */
        foreach ($orderCollection as $order) {
            $eventsBags[Tracker::ORDER_EVENT_PLACED]->add(new OrderEvent($order, $order->getCreatedAt()));

            if ($order->getStateMachineState()->getTechnicalName() === OrderStates::STATE_COMPLETED) {
                $happenedAt = $order->getStateMachineState()->getCreatedAt();
                $eventsBags[Tracker::ORDER_EVENT_FULFILLED]->add(new OrderEvent($order, $happenedAt));
            }

            if ($order->getStateMachineState()->getTechnicalName() === OrderStates::STATE_CANCELLED) {
                $happenedAt = $order->getStateMachineState()->getCreatedAt();
                $eventsBags[Tracker::ORDER_EVENT_CANCELED]->add(new OrderEvent($order, $happenedAt));
            }

            if ($order->getStateMachineState()->getTechnicalName() === OrderTransactionStates::STATE_REFUNDED) {
                $happenedAt = $order->getStateMachineState()->getCreatedAt();
                $eventsBags[Tracker::ORDER_EVENT_REFUNDED]->add(new OrderEvent($order, $happenedAt));
            }
        }

        foreach ($eventsBags as $type => $eventsBag) {
            $trackingResult = $this->trackEventBagByType($context, $eventsBag, $type);

            foreach ($trackingResult->getFailedOrdersErrors() as $orderId => $orderErrors) {
                /** @var \Throwable $error */
                foreach ($orderErrors as $error) {
                    $result->addError(new \Exception(
                        \sprintf('Order[id: %s] error: %s', $orderId, $error->getMessage())
                    ));
                }
            }
        }

        return $result;
    }

    private function trackEventBagByType(
        Context $context,
        OrderTrackingEventsBag $eventsBag,
        string $type
    ): OrderTrackingResult {
        switch ($type) {
            case Tracker::ORDER_EVENT_PLACED:
                $trackingResult = $this->eventsTracker->trackPlacedOrders($context, $eventsBag);
                break;
            case Tracker::ORDER_EVENT_CANCELED:
                $trackingResult = $this->eventsTracker->trackCanceledOrders($context, $eventsBag);
                break;
            case Tracker::ORDER_EVENT_REFUNDED:
                $trackingResult = $this->eventsTracker->trackRefundOrders($context, $eventsBag);
                break;
            case Tracker::ORDER_EVENT_FULFILLED:
                $trackingResult = $this->eventsTracker->trackFulfilledOrders($context, $eventsBag);
                break;
            default:
                $trackingResult = new OrderTrackingResult();
                break;
        }

        return $trackingResult;
    }
}
