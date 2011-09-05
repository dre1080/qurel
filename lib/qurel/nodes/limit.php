<?php
namespace Qurel\Nodes;

/**
 * Limit Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class Limit extends Unary
{
    public function __construct($expr)
    {
        $this->expr = new \Qurel\Attributes\BigInteger($expr);
    }
}