<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\MinkExtension\Context\RawMinkContext;

class RedirectContext extends RawMinkContext
{
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
}