<?php

namespace EdmondsCommerce\BehatMagentoOneContext;

class DemonstrationContext extends AbstractMagentoContext
{
    /**
     * @Given /^I take a screenshot of all pages$/
     */
    public function iTakeAScreenshotOfAllPages()
    {
        $pages = $this->getPages();

        foreach ($pages as $key => $page)
        {
            $this->visitPath($page);
            $this->_screenshot->takeScreenshot($key);
        }
    }


    protected function getPages()
    {
        $pages = array(
            ProductContext::SIMPLE_URI,
            ProductContext::CATEGORY_URI,
            ProductContext::CONFIGURABLE_URI,
            ProductContext::BUNDLE_URI,
            ProductContext::GROUPED_URI,
        );

        $result = array();

        foreach($pages as $page)
        {
            if(isset(self::$_magentoSetting[$page]))
            {
                $result[$page] = self::$_magentoSetting[$page];
            }
        }

        return $result;
    }
}