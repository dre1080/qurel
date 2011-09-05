<?php
namespace Qurel\Nodes;

/**
 * SelectCore Node
 * 
 * @category   Qurel
 * @package    Qurel
 * @subpackage Nodes
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.2
 */
class SelectCore extends Node
{
    public $source;
    public $set_quantifier;
    public $top;
    public $projections = array();
    public $wheres      = array();
    public $groups      = array();
    public $having;
    
    public function __construct()
    {
        $this->source = new JoinSource(null);
    }
    
    public function from($value = null)
    {
        return $value ? $this->source->left = $value : $this->source->left;
    }
}