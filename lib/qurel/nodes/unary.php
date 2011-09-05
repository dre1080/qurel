<?php
namespace Qurel\Nodes;

/**
 * Unary Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
abstract class Unary extends Node
{
    public $expr;

    public function __construct($expr)
    {
        $this->expr = $expr;
    }
}