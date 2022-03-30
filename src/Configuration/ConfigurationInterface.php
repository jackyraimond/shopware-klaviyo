<?php

namespace Klaviyo\Integration\Configuration;

interface ConfigurationInterface
{
    public function getPrivateApiKey(): string;

    public function isTrackViewedProduct(): bool;

    public function isTrackAddedToCart(): bool;

    public function isTrackStartedCheckout(): bool;

    public function isTrackPlacedOrder(): bool;

    public function isTrackOrderedProduct(): bool;

    public function isTrackFulfilledOrder(): bool;

    public function isTrackCanceledOrder(): bool;

    public function isTrackRefundedOrder(): bool;

    public function getPublicApiKey(): string;

    public function getSubscribersListName(): string;

    public function getCatalogFeedProductsCount(): int;

    public function getCustomerCustomFieldMapping(): array;

    public function isAfterInteraction(): bool;

    public function isTrackSubscribedToBackInStock(): bool;

    public function getPopUpOpenBtnColor(): string;

    public function getPopUpOpenBtnBgColor(): string;

    public function getPopUpCloseBtnColor(): string;

    public function getPopUpCloseBtnBgColor(): string;

    public function getSubscribeBtnColor(): string;

    public function getSubscribeBtnBgColor(): string;

    public function getPopUpAdditionalClasses(): string;
}
