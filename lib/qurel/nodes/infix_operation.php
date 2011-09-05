<?php
namespace Qurel\Nodes;

/**
 * InfixOperation Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class InfixOperation extends Binary
{
    public $operator;
    
    public function __construct($operator, $left, $right)
    {
        parent::__construct($left, $right);
        $this->operator = $operator;
    }
    
    /* -(  Math Operations  )------------------------------------------------ */
    
    public function plus($other)
    {
        return new Grouping(new Addition($this, $other));
    }
    
    public function minus($other)
    {
        return new Grouping(new Subtraction($this, $other));
    }
    
    public function divide($other)
    {
        return new Division($this, $other);
    }
    
    public function multiply($other)
    {
        return new Multiplication($this, $other);
    }
    
    /* -(  Expressions  )---------------------------------------------------- */
    
    public function count($distinct = false)
    {
        return new Count(array($this), $distinct);
    }
    
    public function sum()
    {
        return new Sum(array($this), new Sql('sum_id'));
    }
    
    public function maximum()
    {
        return new Max(array($this), new Sql('max_id'));
    }
    
    public function minimum()
    {
        return new Min(array($this), new Sql('min_id'));
    }
    
    public function average()
    {
        return new Avg(array($this), new Sql('avg_id'));
    }
    
    
    /* -(  Predications  )--------------------------------------------------- */

    public function eq($right)
    {
        return new Equality($this, $right);
    }

    public function not_eq($right)
    {
        return new NotEqual($this, $right);
    }

    public function gt($right)
    {
        return new GreaterThan($this, $right);
    }

    public function gte($right)
    {
        return new GreaterThanOrEqual($this, $right);
    }

    public function lt($right)
    {
        return new LessThan($this, $right);
    }

    public function lte($right)
    {
        return new LessThanOrEqual($this, $right);
    }

    public function in($right)
    {
        return new In($this, $right);
    }

    public function not_in($right)
    {
        return new NotIn($this, $right);
    }

    public function matches($right)
    {
        return new Matches($this, $right);
    }

    public function does_not_match($right)
    {
        return new DoesNotMatch($this, $right);
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
        return new Ascending($this);
    }

    public function desc()
    {
        return new Descending($this);
    }
    
    
    /* -(  Alias Predications  )--------------------------------------------- */
    
    public function _as($other)
    {
        return new _As($this, new Sql($other));
    }
}

class Multiplication extends InfixOperation
{
    public function __construct($left, $right)
    {
        parent::__construct('*', $left, $right);
    }
}

class Division extends InfixOperation
{
    public function __construct($left, $right)
    {
        parent::__construct('/', $left, $right);
    }
}

class Addition extends InfixOperation
{
    public function __construct($left, $right)
    {
        parent::__construct('+', $left, $right);
    }
}

class Subtraction extends InfixOperation
{
    public function __construct($left, $right)
    {
        parent::__construct('-', $left, $right);
    }
}