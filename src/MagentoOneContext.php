<?php namespace EdmondsCommerce\BehatMagentoOneContext;

class MagentoOneContext extends AbstractMagentoContext
{
    /**
     * @Then I switch to prices including VAT
     */
    public function iSwitchToPricesIncludingVAT()
    {
        $this->_mink->selectOption('custom_vat_selector', 'Included');
        $this->_jsEvents->iWaitForDocumentReady();
    }

    /**
     * @Then I switch to prices excluding VAT
     */
    public function iSwitchToPricesExcludingVAT()
    {
        $this->_mink->selectOption('custom_vat_selector', 'Excluded');
        $this->_jsEvents->iWaitForDocumentReady();
    }

    /**
     * @Then /^I should be on the (.*) page$/
     */
    public function iShouldBeOnThePage($page)
    {
        switch ($page) {
            case 'checkout success':
                $expectedUrl = '/checkout/onepage/success/';
                break;
            default:
                throw new \Exception("Unknown page type $page");
        }

        $currentUrl   = $this->getSession()->getCurrentUrl();
        $baseUrl      = $this->getMinkParameter('base_url');
        $sanitizedUrl = '/' . str_replace($baseUrl, '', $currentUrl);
        if ($sanitizedUrl !== $expectedUrl) {
            throw new \Exception("Expected to be on $expectedUrl\n Actually on $sanitizedUrl");
        }
    }
}