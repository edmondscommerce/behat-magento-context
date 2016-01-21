<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\Context\RawMinkContext;
use EdmondsCommerce\BehatHtmlContext\HTMLContext;
use EdmondsCommerce\BehatHtmlContext\RedirectionContext;
use EdmondsCommerce\BehatJavascriptContext\JavascriptEventsContext;

abstract class AbstractMagentoContext extends RawMinkContext implements Context, SnippetAcceptingContext
{

    protected $_contextsToInclude = [
        'FeatureContext' => '_mink',
        'EdmondsCommerce\BehatMagentoOneContext\CartContext' => '_cart',
        'EdmondsCommerce\BehatMagentoOneContext\CheckoutContext' => '_checkout',
        'EdmondsCommerce\BehatHtmlContext\RedirectionContext' => '_redirect',
        'EdmondsCommerce\BehatJavascriptContext\JavascriptEventsContext' => '_jsEvents',
        'EdmondsCommerce\BehatHtmlContext\HTMLContext' => '_html'
    ];

    /** @var HTMLContext */
    protected $_navigation;

    /** @var MinkContext */
    protected $_mink;

    /** @var CartContext */
    protected $_cart;

    /** @var RedirectionContext */
    protected $_redirect;

    /** @var JavascriptEventsContext */
    protected $_jsEvents;

    /** @var HTMLContext */
    protected $_html;

    /** @var  CheckoutContext */
    protected $_checkout;


    /**
     * This is used to load in the different contexts so they can be used with in the class
     *
     * @param BeforeScenarioScope $scope
     *
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        $contexts = $this->_getArrayOfContexts();
        foreach ($contexts as $context => $classVar)
        {
            $this->$classVar = $environment->getContext($context);
        }
    }

    /**
     * This is used to get an array of context to include
     *
     * @return array
     */
    protected function _getArrayOfContexts()
    {
        $contexts = $this->_contextsToInclude;
        if (!is_array($contexts) || empty($contexts))
        {
            return [];
        }

        $excluded = isset($this->_contextsToExclude) ? $this->_contextsToExclude : [];
        $excluded[] = get_class($this);

        foreach ($excluded AS $contextToExclude)
        {
            if (isset($contexts[$contextToExclude]))
            {
                unset($contexts[$contextToExclude]);
            }
        }

        return $contexts;
    }


}