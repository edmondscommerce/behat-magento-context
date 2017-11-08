# Magento One Context
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
                - EdmondsCommerce\BehatMagentoOneContext\ProductContext
            parameters:
                magentoSettings:                         
                    pathToMage: '/path/to/app/Mage.php'
                    categoryUri: women/new-arrivals.html
                    simpleUri: accessories/eyewear/aviator-sunglasses.html
                    bundleUri: pillow-and-throw-set.html
                    configurableUri: lafayette-convertible-dress.html
                    groupedUri: vase-set.html

```

The ProductContext has additional parameters that allow for easier navigation to different types of product,
when these are not specified they will default to the sample data urls to ease prototyping of modules using the sample data.

### Custom Contexts should extend `RawMinkContext`

`AbstractMagentoContext::gatherContexts()` expects `Behat\MinkExtension\Context\MinkContext` to be specified in the behat.yml

If the default `FeatureContext`, or any other Contexts extend this class too, Behat will complain that "Step 'x' is already defined in Class::method()".

Contexts should therefore extend `Behat\MinkExtension\Context\RawMinkContext` instead