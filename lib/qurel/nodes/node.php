<?php
namespace Qurel\Nodes;

use Qurel\Visitors\Visitor;

/**
 * Node: Abstract base class for all AST nodes
 * 
 * @category   Qurel
 * @package    Qurel
 * @subpackage Nodes
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.2
 */
class Node
{
    /**
     * Accepts the given Visitor
     * 
     * @param  Visitor $visitor
     * 
     * @return mixed Return value of the Visitor's visit() method
     */
    public function accept(Visitor $visitor)
    {
        return $visitor->visit($this);
    }
    
    /**
     * Factory method to create a Qurel\Nodes\Not node that has the recipient of
     * the caller as a child.
     * 
     * @return Qurel\Nodes\Not 
     */
    public function not()
    {
        return new Not($this);
    }
    
    /**
     * Factory method to create a Qurel\Nodes\Grouping node that has an Qurel\Nodes\Or
     * node as a child.
     * 
     * @param Qurel\Nodes\Node $right
     * 
     * @return Qurel\Nodes\Grouping 
     */
    public function _or($right)
    {
        return new Grouping(new _Or($this, $right));
    }
    
    /**
     * Factory method to create an Qurel\Nodes\And node.
     * 
     * @param Qurel\Nodes\Node $right
     * 
     * @return Qurel\Nodes\_And 
     */
    public function _and($right)
    {
        return new _And(array($this, $right));
    }
}