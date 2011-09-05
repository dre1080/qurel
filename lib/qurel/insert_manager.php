<?php
namespace Qurel;

/**
 * Manages INSERT Queries
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class InsertManager extends TreeManager
{
    public function __construct()
    {
        parent::__construct();
        $this->ast = new Nodes\InsertStatement;
        $this->ctx =& $this->ast;
    }

    /**
     * Insert into this relation
     *
     * @param  Table  $relation
     * @return object           InsertManager
     */
    public function into(Table $relation)
    {
        $this->ast->relation = $relation;
        return $this;
    }

    /**
     * Set values
     *
     * @param  array $values
     */
    public function values($values)
    {
        $this->ast->values = (array)$values;
        return $this;
    }

    /**
     * Gets columns for this insert
     *
     * @param  array   $columns
     * 
     * @return object
     */
    public function columns($columns = array())
    {
        if (!empty($columns)) {
            $columns = (array)$columns;
            foreach ($columns as &$column) {
                if (!$column instanceof Attributes\Attribute) {
                    $column = new Nodes\UnqualifiedColumn(new Attributes\Attribute($this->ast->relation, $column));
                }
                $this->ast->columns[] = $columns;
            }
            
            return $this;
        }
        
        return $this->ast->columns;
    }
    
    public function insert($fields)
    {
        if (empty($fields)) {
            return $this;
        }
        
        if (is_string($fields)) {
            $this->ast->values = new Nodes\SqlLiteral($fields);
        } else {
            $this->ast->relation || $this->ast->relation = reset(reset($fields))->relation;
            
            $columns = array_keys($fields);
            $values  = array_values($fields);
            
            $this->columns($columns);
            $this->ast->values = $this->create_values($values, $this->ast->columns);
        }
        
        return $this->to_sql();
    }
    
    public function create_values($values, $columns)
    {
        return new Nodes\Values($values, $columns);
    }

    /**
     * Get values from this Select Query
     *
     * @param  SelectManager $select
     * 
     * @return InsertManager
     */
    public function select($select)
    {
        if (!$select instanceof SelectManager && !$select instanceof Nodes\SelectStatement) {
            throw new \InvalidArgumentException(
                    'Arel\InsertManager::select() only accepts an object instance of Arel\SelectManager or Arel\Nodes\SelectStatement');
        }
        
        $this->ast->values = $select instanceof SelectManager ? $select->ast : $select;
        return $this;
    }
}
