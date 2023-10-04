<?php

namespace Ecommage\CustomAmastyXsearch\Model\ResourceModel\Posts;

class Collection extends \Amasty\Blog\Model\ResourceModel\Posts\Collection
{
    /**
     * @param $value
     *
     * @return \Amasty\Blog\Model\ResourceModel\Posts\Collection
     */
    public function loadByQueryTextTitle($value)
    {
        $this->getSelect()
             ->where('main_table.title LIKE ?', '%' . $value . '%')
             ->orWhere('main_table.full_content LIKE ?', '%' . $value . '%')
             ->order(new \Zend_Db_Expr("CASE
    WHEN (main_table.title LIKE \"%" . $value . "%\" AND main_table.full_content LIKE \"%" . $value . "%\") THEN 1
    WHEN (main_table.title LIKE \"%" . $value . "%\" AND main_table.full_content NOT LIKE \"%" . $value . "%\") THEN 2
    ELSE 3
    END, main_table.title"));

        return $this;
    }

    protected function renderFilters(): void
    {
        if ($this->getQueryText()) {
            $queryText = $this->getConnection()->quote('%' . $this->getQueryText() . '%');
            $allColumns = $this->getFulltextIndexColumns($this->getStoreTable() ?: $this->getMainTable());
            $condition = '';
            foreach ($allColumns as $key => $column) {
                $column = $this->getStoreColumn($column);
                $newColumn = str_replace("noDefaultStore","main_table",$column);
                if ($key < 1) {
                    $condition .= sprintf('%s LIKE %s', $newColumn, $queryText);
                    continue;
                }
                $condition .= sprintf(' OR %s LIKE %s', $newColumn, $queryText);
            }

            if ($allColumns) {
                $this->getSelect()->where($condition);
            }

        }
    }
}
