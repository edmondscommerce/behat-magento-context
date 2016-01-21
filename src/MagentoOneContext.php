<?php namespace EdmondsCommerce\BehatMagentoOneContext;

class MagentoOneContext extends AbstractMagentoContext
{
    /**
     * @Then I switch to prices including VAT
     */
    public function iSwitchToPricesIncludingVAT()
    {
        $this->_mink->selectOption('custom_vat_selector','Included');
        $this->_jsEvents->iWaitForDocumentReady();
    }

    /**
     * @Then I switch to prices excluding VAT
     */
    public function iSwitchToPricesExcludingVAT()
    {
        $this->_mink->selectOption('custom_vat_selector','Excluded');
        $this->_jsEvents->iWaitForDocumentReady();
    }
}