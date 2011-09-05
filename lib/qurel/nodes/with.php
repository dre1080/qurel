<?php
namespace Qurel\Nodes;

/**
 * With Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class With extends Unary
{
    public $children;
    
    public function __construct($expr)
    {
        parent::__construct($expr);
        $this->children =& $this->expr;
    }
}

class WithRecursive extends With {}