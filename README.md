# WalkTheChat Module (Magento 2)
Bi-directional integration of orders, products and shipping table rates between Magento 2 and WalkTheChat CMS. 

## Possibilities
1. Export products (Simple, Configurable and Virtual) to WalkTheChat CMS with fully currency rate exchange support.
2. Update product inventory on WalkTheChat CMS when was ordered in Magento 2 store.
3. Remove products in WalkTheChat CMS directly from Magento 2 Store.
4. Import orders from WalkTheChat storefront to Magento 2 store with fully currency rate exchange support.
5. Refund, Fulfill or Cancel (todo) orders imported from WalkTheChat storefront.

## How to Install the Module
1. composer require walkthechat/magento2-plugin-v2
2. rm -rf var/* generated/*
3. bin/magento setup:upgrade

## How to Configure the Plugin
1. In your WalktheChat back-end, go to "Settings" / "Third-party App integration" and generate a new App
2. On your Magento 2 store, go to "WalktheChat" / "Configuration section"
3. Enter your WalktheChat store name
4. In the "API Url" field, enter "https://api.walkthechat.com/api/v1/" (don't ommit the final "/")
5. In the "Authorization Url" field, enter "https://cms.v3.walkthechat.com/third-party-apps/auth"
6. Enter your AppId and AppSecret found in your WalktheChat Third-party App
7. Click "Save Config"
8. Click "Connect"

## How to set the Order Webhook
1. In your WalktheChat back-end, go to "Settings" / "Third-party App integration"
2. Open the App you previously created
3. Enter "*magento_domain_name*/rest/v1/walkthechat/import/order" in the "Orders Webhook" field where *magento_domain_name* is the url or your Magento site (starting with "http://" or https://)
