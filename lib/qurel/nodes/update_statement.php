<?php
namespace Qurel\Nodes;

/**
 * UpdateStatement Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class UpdateStatement extends Node
{
    public $relation = null;
    public $limit    = null;
    public $key      = null;
    public $wheres   = array();
    public $orders   = array();
    public $values   = array();
}