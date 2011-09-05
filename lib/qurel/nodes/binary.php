<?php
namespace Qurel\Nodes;

/**
 * Binary
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
abstract class Binary extends Node
{
    public $left;
    public $right;

    public function __construct($left, $right)
    {
        $this->left  = $left;
        $this->right = $right;
    }
}