<?php
namespace Qurel\Visitors;

/**
 * WhereSql
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class WhereSql extends Sql
{
    protected function visit_Qurel_Nodes_SelectCore(\Qurel\Nodes\SelectCore $o)
    {
        return 'WHERE ' . implode(' AND ', $this->visit_each($o->wheres));
    }
}