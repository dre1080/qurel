<?php
namespace Qurel\Nodes;

/**
 * DeleteStatement Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class DeleteStatement extends Binary
{
    public $relation;
    public $limit;
    public $offset;
    public $orders = array();
    public $wheres = array();
    
    public function __construct(\Qurel\Table $relation = null, array $wheres = array())
    {
        parent::__construct($relation, $wheres);
        $this->relation =& $this->left;
        $this->wheres   =& $this->right;
    }
}
