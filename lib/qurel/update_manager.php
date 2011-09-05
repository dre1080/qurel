<?php
namespace Qurel;

/**
 * Manages UPDATE Queries
 *
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class UpdateManager extends TreeManager
{
    public function __construct($engine)
    {
        parent::__construct($engine);
        $this->ast = new Nodes\UpdateStatement;
        $this->ctx =& $this->ast;
    }

    /**
     * Which table should be updated
     *
     * @param  mixed $relation
     * @return UpdateManager
     */
    public function table($relation)
    {
        $this->ast->relation = $relation;
        return $this;
    }
    
    public function key($key = null)
    {
        return $key ? $this->ast->key = $key : $this->ast->key;
    }

    /**
     * Values for the Update
     *
     * @param  array $values Column-Value-Pairs
     * @return UpdateManager
     */
    public function set($values)
    {
        if (is_string($values)) {
            $this->ast->values = $values;
        } else {
            foreach ($values as $column => $value) {
                if (!$column instanceof Attributes\Attribute) {
                    $column = new Attributes\Attribute($this->ast->relation, $column);
                }

                $this->ast->values[] = new Nodes\Assignment(new Nodes\UnqualifiedColumn($column), $value);
            }
        }
        
        return $this;
    }

    /**
     * Adds an Order Expression
     *
     * @param  Node|Attribute $expr
     * @param  int            $direction
     * @return UpdateManager
     */
    public function order($expr)
    {
        if ($expr === null) {
            return $this;
        }
        
        $this->ast->orders[] = $expr instanceof Nodes\Ordering ? 
                               $expr : new Nodes\Ascending($expr);
        
        return $this;
    }

    public function take($limit)
    {
        $this->ast->limit = $limit ? new Nodes\Limit((int)$limit) : null;
        return $this;
    }

    public function skip($amount)
    {
        $this->ast->offset = $amount ? new Nodes\Offset((int)$amount) : null;
        return $this;
    }
}
