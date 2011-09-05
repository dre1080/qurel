<?php
namespace Qurel;

/**
 * Manages DELETE Queries
 *
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class DeleteManager extends TreeManager
{
    public function __construct()
    {
        parent::__construct();
        $this->ast = new Nodes\DeleteStatement;
        $this->ctx =& $this->ast;
    }

    public function from($relation)
    {
        $this->ast->relation = $relation;
        return $this;
    }
    
    public function wheres($list)
    {
        $this->ast->wheres = $list;
        return $this;
    }

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
