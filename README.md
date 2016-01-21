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

```
