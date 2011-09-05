<?php
namespace Qurel\Nodes;

/**
 * Function Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
abstract class Func extends Node
{
    public $alias;
    public $expressions;
    
    public function __construct($expr, $alias = null)
    {
        $this->expressions = $expr;
        
        if ($alias) {
            $this->alias = new Sql($alias);
        }
    }
    
    public function _as($alias)
    {
        $this->alias = new Sql($alias);
        return $this;
    }
}