<?php
namespace Qurel\Nodes;

/**
 * Sql literal Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class Sql extends Unary
{
    public function __construct($expr)
    {
        parent::__construct((string)$expr);
    }
    
    public function __toString()
    {
        return $this->expr;
    }
}
