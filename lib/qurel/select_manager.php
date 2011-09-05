<?php
namespace Qurel;

use Qurel;
use Qurel\Nodes\_And;
use Qurel\Nodes\Ascending;
use Qurel\Nodes\Exists;
use Qurel\Nodes\Group;
use Qurel\Nodes\Grouping;
use Qurel\Nodes\Having;
use Qurel\Nodes\Intersect;
use Qurel\Nodes\Join;
use Qurel\Nodes\Limit;
use Qurel\Nodes\Lock;
use Qurel\Nodes\Ordering;
use Qurel\Nodes\On;
use Qurel\Nodes\Offset;
use Qurel\Nodes\SelectStatement;
use Qurel\Nodes\Sql;
use Qurel\Nodes\TableAlias;
use Qurel\Nodes\Top;
use Qurel\Nodes\Union;
use Qurel\Visitors\WhereSql;

/**
 * Manages SELECT Queries.
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class SelectManager extends TreeManager
{
    public function __construct($engine, $table = null)
    {
        parent::__construct($engine);
        $this->ast = new SelectStatement;
        $this->ctx = end($this->ast->cores);
        $this->from($table);
    }
    
    public function __clone()
    {
        parent::__clone();
        $this->ctx  = end($this->ast->cores);
    }
    
    public function limit($amount = null)
    {
        return $amount ? $this->take($amount) : (bool)($this->ast->limit && $this->ast->limit->expr);
    }
    
    public function offset($amount = null)
    {
        return $amount ? $this->skip($amount) : (bool)($this->ast->offset && $this->ast->offset->expr);
    }
    
    public function constraints()
    {
        return $this->ctx->wheres;
    }
    
    public function lock($locking = null)
    {
        if ($locking === null || $locking === true) {
            $locking = Qurel::sql('FOR UPDATE');
        } elseif (is_string($locking) || $locking instanceof Sql) {
            $locking = Qurel::sql($locking);
        }
        
        $this->ast->lock = new Lock($locking);
        return $this;
    }
    
    public function _as($other)
    {
        return new TableAlias(new Grouping($this->ast), new Sql($other));
    }
    
    /**
     * Adds a Limit Expression
     *
     * @param  int $amount
     * @return SelectManager
     */
    public function take($limit)
    {
        $this->ast->limit = $limit ? new Limit($limit) : null;
        $this->ctx->top   = $limit ? new Top($limit)   : null;
        return $this;
    }

    /**
     * Adds an Offset Expression
     *
     * @param  int $amount
     * @return SelectManager
     */
    public function skip($amount)
    {
        $this->ast->offset = $amount ? new Offset((int)$amount) : null;
        return $this;
    }
    
    /**
     * Produces an Arel\Nodes\Exists node
     * 
     * @return Arel\Nodes\Exists 
     */
    public function exists()
    {
        return new Exists($this->ast);
    }
    
    /**
     * Adds Join Constraints to the last added Join Source
     *
     * @param  Node $expr,...
     * @return SelectManager
     */
    public function on()
    {
        $exprs = call_user_func(array($this, '_collapse'), func_get_args());
        $last = end($this->ctx->source->right);
        $last->right = new On($exprs);
        return $this;
    }

    /**
     * Which relation should be selected?
     *
     * @param string $table
     */
    public function from($table)
    {
        $table = is_string($table) ? new Sql($table) : $table;
        if ($table instanceof Join) {
            $this->ctx->source->right[] = $table;
        } else {
            $this->ctx->source->left = $table;
        }
        
        return $this;
    }
    
    public function union($operation)
    {
        return new Union($this->ast, $operation->ast);
    }
    
    public function intersect($other)
    {
        return new Intersect($this->ast, $other->ast);
    }
    
    public function except($other)
    {
        return new Except($this->ast, $other->ast);
    }
    
    public function minus($other)
    {
        return $this->except($other);
    }
    
    public function with()
    {
        $subqueries = func_get_args();
        $node_class = '';
        
        if (is_string($subqueries[0])) {
            $node_class = array_shift($subqueries);
            $node_class = 'Qurel\Nodes\With'.ucfirst(strtolower($node_class));
        } else {
            $node_class = 'Qurel\Nodes\With';
        }
        
        $this->ast->with = new $node_class($subqueries);
        return $this;
    }
    
    public function where_sql()
    {
        if (empty($this->ctx->wheres)) {
            return null;
        }
        
        $visitor = new WhereSql($this->engine);
        return new Sql($visitor->accept($this->ctx));
    }

    /**
     * Joins the selected relation with another relation, in the
     * given mode
     *
     * @param  mixed  $relation
     * @param  string $klass     Join class (Nodes\InnerJoin, Nodes\OuterJoin, Nodes\StringJoin)
     * @param  int   $mode     Nodes\Join Mode (Nodes\Join::INNER, Nodes\Join::OUTER, Nodes\Join::LEFT)
     * @return SelectManager
     */
    public function join($relation, $klass = Nodes\InnerJoin)
    {
        if (is_string($relation) || $relation instanceof Sql) {
            if (empty($relation)) {
                throw new \Exception;
            }
            $klass = Nodes\StringJoin;
        }
        $this->ctx->source->right[] = new $klass($relation, null);
        return $this;
    }
    
    public function join_sources()
    {
        return $this->ctx->source->right;
    }
    
    public function having()
    {
        $exprs = call_user_func(array($this, '_collapse'), func_get_args(), $this->ctx->having);
        $this->ctx->having = new Having($exprs);
        return $this;
    }
    
    /**
     * Adds the given nodes to the list of projections for this Query
     *
     * @param  array $projections|Node $projection,...
     * @return SelectManager
     */
    public function project(/*$projections*/)
    {
        foreach (func_get_args() as $projection) {
            $this->ctx->projections[] = $projection;
        }
        
        return $this;
    }
    
    public function order(/*$expr*/)
    {
        foreach (func_get_args() as $expr) {
            if ($expr instanceof Ordering) {
                $this->ast->orders[] = $expr;
            } else if ($expr !== null) {
                $this->ast->orders[] = new Ascending($expr);
            }
        }
        return $this;
    }
    
    public function orders()
    {
        return $this->ast->orders;
    }

    /**
     * Adds a Group expression
     * 
     * @param  mixed $columns
     * 
     * @return SelectManager
     */
    public function group(/*$columns*/)
    {
        foreach (func_get_args() as $column) {
            if (is_string($column)) {
                $column = new Sql($column);
            }
            $this->ctx->groups[] = new Group($column);
        }
        return $this;
    }
    
    private function _collapse(array $exprs, $existing = null)
    {
        if ($existing) {
            array_unshift($exprs, $existing->expr);
        }
        
        foreach ($exprs as &$expr) {
            if (is_string($expr)) {
                $expr = Qurel::sql($expr);
            }
        }
        
        if (count($exprs) == 1) {
            return reset($exprs);
        }
        
        return new _And($exprs);
    }
}
