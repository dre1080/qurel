<?php
namespace Qurel\Visitors;

use Qurel;
use Qurel\Nodes\Limit;
use Qurel\Nodes\SelectCore;
use Qurel\Nodes\SelectStatement;
use Qurel\Nodes\UpdateStatement;

/**
 * Visitor for MySQL engine.
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class MySQL extends Sql
{
    /**
     * Fixes statement when there is an offset but no limit. Read below:
     * 
     * To retrieve all rows from a certain offset up to the end of the result set, 
     * you can use some large number for the second parameter.
     * 
     * This statement retrieves all rows from the 96th row to the last:
     *     SELECT * FROM tbl LIMIT 95,18446744073709551615;
     * 
     * With one argument, the value specifies the number of rows to return from the beginning of the result set:
     *     SELECT * FROM tbl LIMIT 5;     # Retrieve first 5 rows
     * 
     * In other words, LIMIT row_count is equivalent to LIMIT 0, row_count.
     * 
     * @see {@link http://dev.mysql.com/doc/refman/5.0/en/select.html}
     * 
     * @param  \Qurel\Nodes\SelectStatement $o The select statement
     * 
     * @return string The select statement string
     */
    protected function visit_Qurel_Nodes_SelectStatement(SelectStatement $o)
    {
        if ($o->offset && !$o->limit) {
            $o->limit = new Limit(Qurel::sql('18446744073709551615'));
        }
        
        return parent::visit_Qurel_Nodes_SelectStatement($o);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function visit_Qurel_Nodes_Union(\Qurel\Nodes\Union $o, $supress = false)
    {
        $left = $right = '';
        
        if ($o->left instanceof \Qurel\Nodes\Union) {
            $left = $this->visit_Qurel_Nodes_Union($o->left, true);
        } else {
            $left = $this->visit($o->left);
        }
        
        if ($o->right instanceof \Qurel\Nodes\Union) {
            $right = $this->visit_Qurel_Nodes_Union($o->right, true);
        } else {
            $right = $this->visit($o->right);
        }
        
        if ($supress) {
            return "$left UNION $right";
        }
        
        return "( $left UNION $right )";
    }
    
    /**
     * {@inheritdoc}
     */
    protected function visit_Qurel_Nodes_Bin(\Qurel\Nodes\Bin $o)
    {
        return "BINARY {$this->visit($o->expr)}";
    }
    
    /**
     * {@inheritdoc}
     */
    protected function visit_Qurel_Nodes_Lock(\Qurel\Nodes\Lock $o)
    {
        return $this->visit($o->expr);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function visit_Qurel_Nodes_SelectCore(SelectCore $o)
    {
        // You must use the DUAL pseudo table when you select a literal that includes a WHERE clause
        @$o->from()->name || $o->from(Qurel::sql('DUAL'));
        return parent::visit_Qurel_Nodes_SelectCore($o);
    }
    
    /**
     * {@inheritdoc}
     */
    protected function visit_Qurel_Nodes_UpdateStatement(UpdateStatement $o)
    {
        return implode(' ', array_filter(array(
            "UPDATE {$this->visit($o->relation)}",
            (empty($o->values) ? null : 'SET ' . implode(', ', $this->visit_each($o->values))),
            (empty($o->wheres) ? null : 'WHERE (' . implode(') AND (', $this->visit_each($o->wheres)) .')'),
            (empty($o->orders) ? null : 'ORDER BY ' . implode(', ', $this->visit_each($o->orders))),
            ($o->limit ? $this->visit($o->limit) : null)
        )));
    }
}