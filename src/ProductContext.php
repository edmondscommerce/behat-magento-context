<?php namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Mink\Element\NodeElement;
use InvalidArgumentException;

class ProductContext extends AbstractMagentoContext
{
    /**
     * Choose an option for a configurable product
     *
     * @Then /^I choose product option "([^"]*)"$/
     */
    public function iChooseProductOptionFor($option)
    {
        //Get the container
        $session = $this->getSession();
        $optionContainer = $session->getPage()->findById('product-options-wrapper');

        /** @var NodeElement[] $values */
        $values = $optionContainer->findAll('xpath', 'dl/dd/div/ul/li/a');

        foreach ($values as $v)
        {
            if ($v->getAttribute('name') == $option)
            {
                //Chose the option
                $v->click();
                return;
            }
        }

        throw new InvalidArgumentException(sprintf('Could not find a product option: "%s"', $option));
    }
}