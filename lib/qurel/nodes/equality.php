<?php
namespace Qurel\Nodes;

/**
 * Equality Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class Equality extends Binary
{
    public $operator = '==';
    public $operand1;
    public $operand2;
    
    public function __construct($left, $right)
    {
        parent::__construct($left, $right);
        $this->operand1 =& $this->left;
        $this->operand2 =& $this->right;
    }
}
