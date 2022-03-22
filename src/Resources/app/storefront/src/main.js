import KlaviyoIdentityTrackingComponent from './klaviyo/events-tracking/identity-tracking-component';
import KlaviyoProductViewedEventTrackingComponent from './klaviyo/events-tracking/product-viewed-event-tracking-component';
import KlaviyoCheckoutStartedEventTrackingComponent from './klaviyo/events-tracking/checkout-started-event-tracking-component';
import KlaviyoTracking from "./klaviyo/plugins/klaviyo.plugin";
import KlaviyoBackInStockTrackingComponent from './klaviyo/events-tracking/back-in-stock-event-tracking-component';

const PluginManager = window.PluginManager;
PluginManager.register(
    'KlaviyoIdentityTrackingComponent',
    KlaviyoIdentityTrackingComponent,
    '[data-klaviyo-identity-tracking-component]'
);
PluginManager.register(
    'KlaviyoProductViewedEventTrackingComponent',
    KlaviyoProductViewedEventTrackingComponent,
    '[data-klaviyo-product-viewed-event-tracking-component]'
);
PluginManager.register(
    'KlaviyoCheckoutStartedEventTrackingComponent',
    KlaviyoCheckoutStartedEventTrackingComponent,
    '[data-klaviyo-checkout-started-event-tracking-component]'
);
PluginManager.register(
    'KlaviyoTracking',
    KlaviyoTracking,
    '[data-klaviyo-tracking]'
);
PluginManager.register(
    'KlaviyoBackInStockTrackingComponent',
    KlaviyoBackInStockTrackingComponent,
    '[data-klaviyo-back-in-stock-subscriber-event-tracking-component]'
)
