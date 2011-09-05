<?php
namespace Qurel\Nodes;

/**
 * Values Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class Values extends Binary
{
    public $expressions;
    public $columns = array();
    
    public function __construct($exprs, array $columns = array())
    {
        parent::__construct($exprs, $columns);
        $this->expressions =& $this->left;
        $this->columns     =& $this->right;
    }
}