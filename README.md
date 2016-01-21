#Magento One Context
## By [Edmonds Commerce](https://www.edmondscommerce.co.uk)

Behat contexts to aid testing of Magento 1.x sites

### Installation

Install via composer

"edmondscommerce/behat-magento-one-context": "~1.1"


### Include Contexts in Behat Configuration

```
default:
    # ...
    suites:
        default:
            # ...
            contexts:
                - # ...
                - EdmondsCommerce\BehatMagentoOneContext\CartContext
                - EdmondsCommerce\BehatMagentoOneContext\CustomerContext
                - EdmondsCommerce\BehatMagentoOneContext\NavigationContext
                - EdmondsCommerce\BehatMagentoOneContext\ProductContext:
                    categoryUri: women/new-arrivals.html
                    simpleUri: accessories/eyewear/aviator-sunglasses.html
                    bundleUri: pillow-and-throw-set.html
                    configurableUri: lafayette-convertible-dress.html
                    groupedUri: vase-set.html
```

The ProductContext has additional paramaters that allow for easier navigation to different types of product,
when these are not specified they will default to the sample data urls to ease prototyping of modules using the sample data.