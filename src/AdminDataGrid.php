<?php

namespace EdmondsCommerce\BehatMagentoOneContext;

use Exception;

/**
 * Class AdminDataGrid
 * @package EdmondsCommerce\BehatMagentoOneContext
 * Object to represent data grid
 */
class AdminDataGrid
{
    /**
     * @var array
     */
    private $table;

    /**
     * @var array
     */
    private $columns;

    /**
     * AdminDataGrid constructor.
     * @param array $columns
     * @param array $table
     */
    public function __construct(array $columns, array $table)
    {
        $this->table = $table;
        $this->columns = $columns;
    }


    /**
     * @param \Behat\Gherkin\Node\TableNode $table
     * @throws Exception
     */
    public function assertContainsTable(\Behat\Gherkin\Node\TableNode $table)
    {
        //Check the columns exist
        $inputRows   = $table->getHash();
        $columnNames = $table->getTable();
        $columnNames = array_shift($columnNames);

        //Filter down to the columns we are checking
        $grid = $this->onlyColumns($columnNames);

        //Go through each row, check them in turn
        foreach ($inputRows as $row)
        {
            $grid->assertTableContainsRow($row);
        }
    }

    /**
     * Checks that there is an occurrence of the given row, matching the specified columns
     * @param array $row
     * @throws Exception
     */
    public function assertTableContainsRow(array $row)
    {
        //Go through each row in our table
        foreach ($this->table as $tableRow)
        {
            //Check each corresponding cell
            $found = true;
            foreach ($row as $cellName => $cellValue)
            {
                $tableRowValue = $tableRow[$cellName];
                //Mismatch, skip the rest of the checks for this row
                if ($tableRowValue !== $cellValue)
                {
                    $found = false;
                    break;
                }
            }

            //Did we get to the end of the checks for a row?
            if (!$found)
            {
                //No, lets check the next row
                continue;
            }

            return;
        }

        //Didn't find a matching row
        throw new \Exception('Could not find row contains elements: ' . json_encode($row));
    }

    /**
     * Returns a reduced data grid including only the columns given
     * @throws Exception
     * @return AdminDataGrid
     */
    public function onlyColumns(array $columns)
    {
        $this->assertColumnsExist($columns);
        $result = array_map(function ($row) use ($columns)
        {
            return array_filter($row, function ($key) use ($columns)
            {
                return (in_array($key, $columns));
            }, ARRAY_FILTER_USE_KEY);
        }, $this->table);

        return new AdminDataGrid($columns, $result);
    }

    public function hasColumn($name)
    {
        return (in_array($name, $this->columns));
    }

    /**
     * @param array $names
     * @throws Exception
     */
    public function assertColumnsExist(array $names)
    {
        foreach ($names as $name)
        {
            if (!$this->hasColumn($name))
            {
                throw new Exception('Column "' . $name . '"" does not exist');
            }
        }
    }
}