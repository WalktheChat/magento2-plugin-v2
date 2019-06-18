#WalkTheChat Module (Magento 2)
Bi-directional integration of orders, products and shipping table rates between Magento 2 and WalkTheChat CMS. 

##Possibilities
1. Export products (Simple, Configurable and Virtual) to WalkTheChat CMS with fully currency rate exchange support.
2. Update product inventory on WalkTheChat CMS when was ordered in Magento 2 store.
3. Remove products in WalkTheChat CMS directly from Magento 2 Store.
4. Import orders from WalkTheChat storefront to Magento 2 store with fully currency rate exchange support.
5. Refund, Fulfill or Cancel (todo) orders imported from WalkTheChat storefront.

##How to Install the Module
1. composer require walkthechat/magento2-plugin-v2
2. rm -rf var/* generated/*
3. bin/magento setup:upgrade
