<?php namespace EdmondsCommerce\BehatMagentoOneContext;

class PaypalContext extends AbstractMagentoContext
{
    /**
     * @When I login into paypal
     * @throws \Exception
     */
    public function iLoginIntoPaypal()
    {
        $payPalEmail    = self::$_magentoSetting['payPalEmail'];
        $payPalPassword = self::$_magentoSetting['payPalPassword'];

        if ($payPalEmail === null || $payPalPassword === null) {
            throw new \RuntimeException('You must set the PayPal settings in the behat.yaml file');
        }

        $session = $this->getSession();
        $name    = $session->getPage()->find('css', '#email');
        $name->setValue($payPalEmail);

        $password = $session->getPage()->find('css', '#password');
        $password->setValue($payPalPassword);

        $session->getPage()->find('css', '#btnLogin')->click();
    }

    /**
     * @When I Click PayNow
     */
    public function iClickPayNow()
    {
        // if this payment screen
        $button = $this->getSession()->getPage()->find('css', 'input[type="submit"]');

        if ($button === null) {
            // this is select payment source screen
            $sourceButton = $this->getSession()->getPage()->find('css', '#button button');
            $sourceButton->click();
            sleep(5);
            // now this is payment screen
            $button = $this->getSession()->getPage()->find('css', 'input[type="submit"]');
        }

        $button->click();
    }
}
