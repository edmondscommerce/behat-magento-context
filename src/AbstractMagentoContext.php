<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\Context\RawMinkContext;

abstract class AbstractMagentoContext extends RawMinkContext implements Context, SnippetAcceptingContext
{

    protected $_contextsToInclude = [
        'FeatureContext'             => '_mink'
    ];

    /** @var  BasicPageNavigationContext */
    protected $_pageNavigation;

    /** @var  MinkContext */
    protected $_mink;

    /**
     * This is used to load in the different contexts so they can be used with in the class
     *
     * @param BeforeScenarioScope $scope
     *
     * @BeforeScenario
     */
    protected function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        $contexts    = $this->_getArrayOfContexts();
        foreach ($contexts as $context => $classVar) {

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
        if (!is_array($contexts) || empty($contexts)) {
            return [];
        }

        $excluded   = isset($this->_contextsToExclude) ? $this->_contextsToExclude : [];
        $excluded[] = get_class($this);

        foreach ($excluded AS $contextToExclude) {
            if (isset($contexts[$contextToExclude])) {
                unset($contexts[$contextToExclude]);
            }
        }

        return $contexts;
    }

    public function canIntercept()
    {
        $driver = $this->getSession()->getDriver();
        if (!$driver instanceof GoutteDriver) {
            throw new UnsupportedDriverActionException(
                'You need to tag the scenario with "@mink:goutte" Intercepting the redirections is not supported by %s',
                $driver
            );
        }
    }

    /**
     * @Given I don't follow redirects
     */
    public function redirectsAreIntercepted()
    {
        $this->getSession()->getDriver()->getClient()->followRedirects(false);

    }

    /**
     * @When /^I follow the redirection$/
     * @Then /^I should be redirected$/
     */
    public function iFollowTheRedirection()
    {
        $this->canIntercept();
        $client = $this->getSession()->getDriver()->getClient();
        $client->followRedirects(true);
        $client->followRedirect();
    }

    /**
     * @Then /^I wait for AJAX to finish$/
     */
    public function iWaitForAjaxToFinish()
    {
        $this->getSession()->wait(10000, '((0 === Ajax.activeRequestCount) && (0 === jQuery.active))');
        $this->getSession()->wait(1000);
    }

    /**
     * @Then /^I wait for AJAX Content to load$/
     */
    public function iWaitForAjaxContentToLoad()
    {
        $this->getSession()->wait(5000);
    }

    /**
     * @Then I wait for the document ready event
     * @Then I wait for the page to fully load
     */
    public function iWaitForDocumentReady()
    {
        $this->getSession()->wait(10000, '("complete" === document.readyState)');
    }

    /**
     * @Then I wait for jQuery to finish loading
     */
    public function iWaitForJQuery()
    {
        $this->getSession()->wait(5000, 'typeof window.jQuery == "function"');
    }
}