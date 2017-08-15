<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use EdmondsCommerce\BehatFakerContext\FakerContext;
use EdmondsCommerce\BehatHtmlContext\HTMLContext;
use EdmondsCommerce\BehatHtmlContext\RedirectionContext;
use EdmondsCommerce\BehatJavascriptContext\JavascriptEventsContext;
use EdmondsCommerce\BehatScreenshotContext\ScreenshotContext;
use Mage;
use Mage_Core_Model_Store;

abstract class AbstractMagentoContext extends RawMinkContext implements Context, SnippetAcceptingContext
{

    /** @var  array */
    protected static $_magentoSetting;
    /** @var CartContext */
    protected $_cart;
    /** @var  CheckoutContext */
    protected $_checkout;
    /** @var array */
    protected $_contextsToInclude = [
        MinkContext::class               => '_mink',
        CartContext::class             => '_cart',
        CheckoutContext::class         => '_checkout',
        RedirectionContext::class      => '_redirect',
        JavascriptEventsContext::class => '_jsEvents',
        HTMLContext::class             => '_html',
        ProductContext::class          => '_product',
        CustomerContext::class         => '_customer',
        ScreenshotContext::class       => '_screenshot',
        CategoryContext::class         => '_category'
    ];
    /** @var HTMLContext */
    protected $_html;
    /** @var JavascriptEventsContext */
    protected $_jsEvents;
    /** @var MinkContext */
    protected $_mink;
    /** @var RedirectionContext */
    protected $_redirect;
    /** @var  ProductContext */
    protected $_product;
    /** @var  CustomerContext */
    protected $_customer;
    /** @var  ScreenshotContext */
    protected $_screenshot;
    /** @var CategoryContext */
    protected $_category;

    /** @BeforeSuite
     * @param BeforeSuiteScope $scope
     *
     * @throws \Exception
     */
    public static function loadMagentoConfiguration(BeforeSuiteScope $scope)
    {
        $environment = $scope->getEnvironment();
        if (!$environment->getSuite()->hasSetting('parameters'))
        {
            throw new \Exception('You must set the parameters scetion of the behat.yml');
        }
        $parameters = $environment->getSuite()->getSetting('parameters');
        if (!isset($parameters['magentoSettings']))
        {
            throw new \Exception('You must include the magentoSetting in the behat.yml file');
        }
        $magentoSetting = $parameters['magentoSettings'];
        $pathToMage = $magentoSetting['pathToMage'];
        if (!file_exists($pathToMage))
        {
            throw new \Exception('You must provide a valid pathToMage path in the behat.yml file');
        }
        self::$_magentoSetting = $magentoSetting;
        self::_loadMageFile();
    }

    /**
     * This is used as a single place to load the MageFile
     */
    protected static function _loadMageFile()
    {
        if (!class_exists('\Mage'))
        {
            $autoLoaders = spl_autoload_functions();
            // We need to get the Mage file so we can use the Magento feature
            // @codingStandardsIgnoreStart
            require_once self::$_magentoSetting['pathToMage'];
            // @codingStandardsIgnoreEnd
            if (\Mage::registry('isSecureArea') !== true)
            {
                \Mage::register('isSecureArea', true);
            }
            \Mage::app()->setCurrentStore(\Mage_Core_Model_App::ADMIN_STORE_ID);
            /*
             * Some modules in Magento declare their own auto-loaders which can clobber the current ones or change their
             * order. This can cause issues when the tests are run. The code below puts them back into the expects order
             *
             * However, if there are no new autoloaders, then this would cause problems, as the stack would have
             * multiple calls to autoloaders in the wrong order. Therefore we will check if the number of autoloaders
             * has increased, in which case we will do nothing, or stayed the same, or gone down when we will add the
             * original ones back
             */
            $newAutoLoaders = spl_autoload_functions();
            if (count($newAutoLoaders) <= count($autoLoaders))
            {
                foreach (array_reverse($autoLoaders) AS $loader)
                {
                    $class = $loader[0];
                    $method = $loader[1];
                    spl_autoload_register(array($class, $method), true, true);
                }
            }
        }
    }

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

    protected function getFrontUrl($route, $queryArgs = '')
    {
        return Mage::getUrl('checkout/cart/index', array(
            '_store' => Mage::app()->getDefaultStoreView()->getId(),
            '_type'  => Mage_Core_Model_Store::URL_TYPE_WEB
        ));
    }
}