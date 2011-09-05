<?php
namespace Qurel\Nodes;

/**
 * TableAlias Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class TableAlias extends Binary implements \ArrayAccess
{
    public $name;
    public $relation;
    public $table_alias;
    
    public function __construct($left, $right)
    {
        parent::__construct($left, $right);
        $this->name        =& $this->right;
        $this->relation    =& $this->left;
        $this->table_alias =& $this->name;
    }
    
    public function table_name()
    {
        return $this->relation->name;
    }
    
    public function __get($name)
    {
        return $this->offsetGet($name);
    }
    
    public function offsetGet($offset)
    {
        return new \Qurel\Attributes\Attribute($this, $offset);
    }
    
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('offsetSet is not available');
    }

    public function offsetExists($offset)
    {
        throw new \BadMethodCallException('offsetExists is not available');
    }

    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('offsetUnset is not available');
    }
}