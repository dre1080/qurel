<?php
namespace Qurel\Nodes;

/**
 * _And Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class _And extends Node
{
    public $children = array();
    
    public function __construct(array $children)
    {
        $this->children = $children;
    }
    
    public function left()
    {
        return reset($this->children);
    }
    
    public function right()
    {
        return $this->children[1];
    }
}
