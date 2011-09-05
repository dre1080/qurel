<?php
namespace Qurel\Nodes;

/**
 * UnqualifiedColumn Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class UnqualifiedColumn extends Unary
{
    public $attribute;
    public $table_name;
    public $name;
    
    public function __construct(\Qurel\Attributes\Attribute $expr)
    {
        parent::__construct($expr);
        $this->attribute =& $this->expr;
        $this->name      =& $this->attribute->name;
        $this->table_name=& $this->attribute->relation->name;
    }
}
