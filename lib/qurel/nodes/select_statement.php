<?php
namespace Qurel\Nodes;

/**
 * SelectStatement Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class SelectStatement extends Node
{
    public $cores  = array();
    public $orders = array();
    public $limit;
    public $offset;
    public $lock;
    public $with;
    
    public function __construct(array $cores = array())
    {
        $this->cores = !empty($cores) ? $cores : array(new SelectCore);
    }
}
