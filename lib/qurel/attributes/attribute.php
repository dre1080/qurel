<?php
namespace Qurel\Attributes;

use Qurel\Table;

/**
 * Attribute
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class Attribute
{
    /**
     * Name of the Attribute
     * 
     * @var string
     */
    public $name;

    /**
     * Relation, which this attribute belongs to
     * 
     * @var Table
     */
    public $relation;

    /**
     * Constructor
     *
     * @param Table  $relation
     * @param string $name
     */
    public function __construct(Table $relation, $name)
    {
        $this->relation = $relation;
        $this->name = $name;
    }

    /**
     * Returns the attributes fully qualified name for debugging
     * 
     * @return string
     */
    public function __toString()
    {
        $join_name = $this->relation->table_alias ? $this->relation->table_alias : $this->relation->name;
        return "$join_name.$this->name";
    }
    
    /**
     * Create a node for lowering this attribute
     * 
     * @return \Qurel\Nodes\NamedFunc
     */
    public function lower()
    {
        return $this->relation->lower($this);
    }
    
    /* -(  Math Operations  )------------------------------------------------ */
    
    public function plus($other)
    {
        return new \Qurel\Nodes\Grouping(new \Qurel\Nodes\Addition($this, $other));
    }
    
    public function minus($other)
    {
        return new \Qurel\Nodes\Grouping(new \Qurel\Nodes\Subtraction($this, $other));
    }
    
    public function divide($other)
    {
        return new \Qurel\Nodes\Division($this, $other);
    }
    
    public function multiply($other)
    {
        return new \Qurel\Nodes\Multiplication($this, $other);
    }
    
    /* -(  Expressions  )---------------------------------------------------- */
    
    public function count($distinct = false)
    {
        return new \Qurel\Nodes\Count(array($this), $distinct);
    }
    
    public function sum()
    {
        return new \Qurel\Nodes\Sum(array($this), new Nodes\Sql('sum_id'));
    }
    
    public function maximum()
    {
        return new \Qurel\Nodes\Max(array($this), new Nodes\Sql('max_id'));
    }
    
    public function minimum()
    {
        return new \Qurel\Nodes\Min(array($this), new Nodes\Sql('min_id'));
    }
    
    public function average()
    {
        return new \Qurel\Nodes\Avg(array($this), new Nodes\Sql('avg_id'));
    }
    
    
    /* -(  Predications  )--------------------------------------------------- */

    public function eq($right)
    {
        return new \Qurel\Nodes\Equality($this, $right);
    }

    public function not_eq($right)
    {
        return new \Qurel\Nodes\NotEqual($this, $right);
    }

    public function gt($right)
    {
        return new \Qurel\Nodes\GreaterThan($this, $right);
    }

    public function gte($right)
    {
        return new \Qurel\Nodes\GreaterThanOrEqual($this, $right);
    }

    public function lt($right)
    {
        return new \Qurel\Nodes\LessThan($this, $right);
    }

    public function lte($right)
    {
        return new \Qurel\Nodes\LessThanOrEqual($this, $right);
    }

    public function in($right)
    {
        return new \Qurel\Nodes\In($this, $right);
    }

    public function not_in($right)
    {
        return new \Qurel\Nodes\NotIn($this, $right);
    }

    public function matches($right)
    {
        return new \Qurel\Nodes\Matches($this, $right);
    }

    public function does_not_match($right)
    {
        return new \Qurel\Nodes\DoesNotMatch($this, $right);
    }
    
    public function like($right)
    {
        return $this->matches($right);
    }

    public function not_like($right)
    {
        return $this->does_not_match($right);
    }
    
    
    /* -(  Order Predications  )--------------------------------------------- */

    public function asc()
    {
        return new \Qurel\Nodes\Ascending($this);
    }

    public function desc()
    {
        return new \Qurel\Nodes\Descending($this);
    }
    
    
    /* -(  Alias Predications  )--------------------------------------------- */
    
    public function _as($other)
    {
        return new \Qurel\Nodes\_As($this, new \Qurel\Nodes\Sql($other));
    }
    
    
    /* -(  Private Methods  )------------------------------------------------ */
    
    /**
     *
     * @param string $method_id
     * @param array  $others
     * 
     * @return \Qurel\Nodes\Grouping 
     */
    private function _grouping_any($method_id, array $others)
    {
        $nodes = array_map(array($this, $method_id), $others);
        $first = array_shift($node);
        $ors   = array_reduce($nodes, function ($memo, $node) use ($first) {
            static $initial = false;
            if (!$initial) {
                $initial = true;
                return new \Qurel\Nodes\_Or($first, $node);
            }
            return new \Qurel\Nodes\_Or($memo, $node);
        });
        
        return new \Qurel\Nodes\Grouping($ors);
    }
    
    /**
     *
     * @param string $method_id
     * @param array  $others
     * 
     * @return \Qurel\Nodes\Grouping 
     */
    private function _grouping_all($method_id, array $others)
    {
        $nodes = array_map(array($this, $method_id), $others);
        return new \Qurel\Nodes\Grouping(new \Qurel\Nodes\_And($nodes));
    }
}
