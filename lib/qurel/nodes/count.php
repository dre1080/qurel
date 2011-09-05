<?php
namespace Qurel\Nodes;

/**
 * Count Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class Count extends Func
{
    public $distinct;
    
    public function __construct($expr, $distinct = false, $alias = null)
    {
        parent::__construct($expr, $alias);
        $this->distinct = $distinct;
    }
}