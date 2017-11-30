<?php

namespace EdmondsCommerce\BehatMagentoOneContext;

use Behat\Mink\Element\NodeElement;

/**
 * Class AdminDataGridContext
 * @package EdmondsCommerce\BehatMagentoOneContext
 * Handles querying admin data grids
 */
class AdminDataGridContext extends AbstractMagentoContext
{
    /**
     * Finds and returns a datagrid on the page, will not work with when multiple grids are present
     * @throws \Behat\Mink\Exception\ExpectationException
     * @return AdminDataGrid
     */
    public function getDataGrid()
    {
        $gridNode = $this->_html->findOneOrFail('xpath', '//div[@class="grid"]');

        //Extract the column names
        $columnNames = $this->_html->findAllOrFailFromNode($gridNode, 'xpath', '//table/thead/tr[@class="headings"]/th');
        $columnNames = array_map(function(NodeElement $node) {
            return $node->getText();
        }, $columnNames);

        //Extract the content
        $rows = $this->_html->findAllOrFailFromNode($gridNode, 'xpath', '//table/tbody/tr');
        $mappedCells = array_map(function(NodeElement $node) use ($columnNames) {

            //Extract the cells
            $cells = $node->findAll('css', 'td');

            //Sanity check the number of cells
            if(count($columnNames) !== count($cells))
            {
                throw new \Exception('Cell and header count mismatch');
            }

            $result = [];
            /** @var NodeElement $cell */
            foreach($cells as $index => $cell)
            {
                $column = $columnNames[$index];
                $result[$column] = $cell->getText();
            }

            return $result;
        }, $rows);

        return new AdminDataGrid($columnNames, $mappedCells);
    }
}