<?php
namespace Qurel\Visitors;

use Qurel\Nodes\Matches;
use Qurel\Nodes\DoesNotMatch;
use Qurel\Nodes\DistinctOn;

/**
 * Visitor for PostgreSQL engine.
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class PostgreSQL extends Sql
{
    /**
     * {@inheritdoc}
     */
    protected function visit_Qurel_Nodes_Matches(Matches $o)
    {
        return "{$this->visit($o->left)} ILIKE {$this->visit($o->right)}";
    }
    
    /**
     * {@inheritdoc}
     */
    protected function visit_Qurel_Nodes_DoesNotMatch(DoesNotMatch $o)
    {
        return "{$this->visit($o->left)} NOT ILIKE {$this->visit($o->right)}";
    }
    
    /**
     * {@inheritdoc}
     */
    protected function visit_Qurel_Nodes_DistinctOn(DistinctOn $o)
    {
        return "DISTINCT ON ( {$this->visit($o->expr)} )";
    }
}