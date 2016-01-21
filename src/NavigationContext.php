<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Exception;

class NavigationContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    /**
     * @When /^(?:|I )click on the element "([^"]*)"$/
     * @param $css
     *
     * @throws Exception
     */
    public function iClickOnTheElement($css)
    {
        $element = $this->getSession()->getPage()->find("css", $css);
        if (!is_object($element)) {
            throw new Exception('Element does not exist');
        }
        $element->click();
    }

    /**
     * @When /^I click on the first link with "([^"]*)" in it$/
     * @param $text
     *
     * @throws Exception
     */
    public function iClickOnTheFirstLinkWithText($text)
    {
        $links = $this->getSession()->getPage()->findAll('css', 'a');
        $linkToClick = false;
        foreach($links as $link) {
            $linkText = $link->getText();
            if($linkText == $text) {
                $linkToClick = $link;
                break;
            }
        }
        if($linkToClick == false) {
            throw new Exception("No link with text $text found");
        }
        $linkToClick->click();

    }

    /**
     * @Then /^I scroll to "([^"]*)"$/
     * @param $elementId
     */
    public function iScrollTo($elementId)
    {
        $this->getSession()->evaluateScript("document.getElementById('" . $elementId . "').scrollIntoView();");
    }

    /**
     * @Then /^I scroll down "([^"]*)" pixels$/
     * @param $pixels
     */
    public function iScrollDown($pixels)
    {
        $this->getSession()->evaluateScript("window.scrollBy(0, " . $pixels . ");");
    }

    /**
     * @When /^(?:|I )confirm the popup$/
     */
    public function confirmPopup()
    {
        $this->getSession()->getDriver()->getWebDriverSession()->accept_alert();
    }

    /**
     * @When /^(?:|I )cancel the popup$/
     */
    public function cancelPopup()
    {
        $this->getSession()->getDriver()->getWebDriverSession()->dismiss_alert();
    }

    /**
     * @When I manually press the :arg1 key
     *
     * @param $key
     */
    public function iManuallyPressTheKey($key) {
        $script = "jQuery.event.trigger({ type : 'keypress', which : '" . $key . "' });";
        $this->getSession()->evaluateScript($script);
    }

    /**
     * @param $css
     * @param $value
     *
     * @throws Exception
     * @When /^Input field "([^"]*)" has a value of "([^"]*)"$/
     */
    public function fieldShouldContain($css, $value)
    {
        $element = $this->getSession()->getPage()->find("css", $css);
        if (!is_object($element)) {
            throw new Exception('Element does not exist');
        }
        $elementValue = $element->getValue();
        if($elementValue != $value) {
            throw new \Exception("values do not match for element $css. Expected $value got $elementValue");
        }
    }
}