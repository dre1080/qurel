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
class NamedFunc extends Func
{
    public $name;
    
    public function __construct($name, $expr, $alias = null)
    {
        parent::__construct($expr, $alias);
        $this->name = $name;
    }
}