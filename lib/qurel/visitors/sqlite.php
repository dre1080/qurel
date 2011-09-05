<?php
namespace Qurel\Visitors;

use Qurel;
use Qurel\Nodes\Limit;
use Qurel\Nodes\Lock;
use Qurel\Nodes\SelectStatement;

/**
 * Visitor for SQLite engine.
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class SQLite extends Sql
{
    /**
     * {@inheritdoc}
     */
    protected function visit_Qurel_Nodes_SelectStatement(SelectStatement $o)
    {
        if ($o->offset && !$o->limit) {
            $o->limit = new Limit(Qurel::sql('-1'));
        }
        
        return parent::visit_Qurel_Nodes_SelectStatement($o);
    }
    
    /**
     * This does nothing on SQLLite3
     */
    protected function visit_Qurel_Nodes_Lock(Lock $o)
    {
        return;
    }
}