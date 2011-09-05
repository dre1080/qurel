<?php
namespace Qurel\Nodes;

/**
 * InsertStatement Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class InsertStatement extends Node
{
    public $relation = null;
    public $values   = array();
    public $columns  = array();
    public $select;
}
