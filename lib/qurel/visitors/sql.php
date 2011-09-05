<?php
namespace Qurel\Visitors;

/**
 * Sql Visitor
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
abstract class Sql extends Visitor
{
    protected $engine;
    
    /**
     * The Constructor
     * 
     * @param string $engine The database engine type
     */
    public function __construct($engine)
    {
        $this->engine = $engine;
    }
    
    /**
     * Creates a DELETE Statement
     *
     * @param  \Qurel\Nodes\DeleteStatement
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_DeleteStatement(\Qurel\Nodes\DeleteStatement $o)
    {
        return implode(' ', array_filter(array(
            "DELETE FROM {$this->visit($o->relation)}",
            (empty($o->wheres) ? null : 'WHERE (' . implode(') AND (', $this->visit_each($o->wheres))) .')',
            (empty($o->orders) ? null : 'ORDER BY ' . implode(', ', $this->visit_each($o->orders))),
            ($o->limit  ? $this->visit($o->limit) : null),
            ($o->offset ? $this->visit($o->offset) : null)
        )));
    }
    
    /**
     * Creates an UPDATE Statement
     *
     * @param  \Qurel\Nodes\UpdateStatement
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_UpdateStatement(\Qurel\Nodes\UpdateStatement $o)
    {
        $wheres = array();
        if (empty($o->orders) && $o->limit === null) {
            $wheres = $o->wheres;
        } else {
            if (!($key = $o->key)) {
                throw new \UnexpectedValueException('Cannot use UpdateManager without setting UpdateManager key.');
            }
            
            $wheres = array(new \Qurel\Nodes\In($key, array($this->_build_subselect($key, $o))));
        }
        
        return implode(' ', array_filter(array(
            "UPDATE {$this->visit($o->relation)}",
            (empty($o->values) ? null : 'SET ' . implode(', ', $this->visit_each($o->values))),
            (empty($wheres) ? null : 'WHERE ' . implode(' AND ', $this->visit_each($wheres)))
        )));
    }
    
    /**
     * Creates an INSERT Statement
     *
     * @param  \Qurel\Nodes\InsertStatement $insert
     * @return string
     */
    protected function visit_Qurel_Nodes_InsertStatement(\Qurel\Nodes\InsertStatement $o)
    {
        return implode(' ', array_filter(array(
            "INSERT INTO {$this->visit($o->relation)}",
            (empty($o->columns) ? null : '(' . implode(', ', $this->visit_each($o->columns)) . ')'),
            ($o->values ? $this->visit($o->values) : null)
        )));
    }
    
    /**
     * Creates an EXISTS Statement
     * 
     * @param  \Qurel\Nodes\Exists $o An Exists node
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Exists(\Qurel\Nodes\Exists $o)
    {
        return "EXISTS ({$this->visit($o->select_stmt)})" .
            ($o->alias ? " AS {$this->visit($o->alias)}" : '');
    }
    
    /**
     * Creates an VALUES Statement
     * 
     * @param  \Qurel\Nodes\Values $o An Values node
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Values(\Qurel\Nodes\Values $o)
    {
        $zipped = $values = array();
        $zipped = array_map(null, $o->expressions, $o->columns);
        
        foreach ($zipped as $pair) {
            $value = array_shift($pair);
            if ($value instanceof \Qurel\Nodes\Sql) {
                $values[] = $this->visit_Qurel_Nodes_Sql($value);
            } else {
                // FIXME: change to $this->quote($value)
                $values[] = "'$value'";
            }
        }
        
        $values = implode(', ', $values);
        return "VALUES ($values)";
    }
    
    /**
     * Get a Full SELECT Query
     *
     * @param  \Qurel\Node\SelectStatement $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_SelectStatement(\Qurel\Nodes\SelectStatement $o)
    {
        return implode(' ', array_filter(array(
            ($o->with  ? $this->visit($o->with)  : null),
            implode(' ', $this->visit_each($o->cores)),
            (empty($o->orders) ? null : 'ORDER BY ' . implode(', ', $this->visit_each($o->orders))),
            ($o->limit  ? $this->visit($o->limit)  : null),
            ($o->offset ? $this->visit($o->offset) : null),
            ($o->lock   ? $this->visit($o->lock)   : null),
        )));
    }
    
    /**
     * Get a Base SELECT Query
     *
     * @param  \Qurel\Node\SelectCore $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_SelectCore(\Qurel\Nodes\SelectCore $o)
    {
        return implode(' ', array_filter(array(
            'SELECT',
            ($o->top  ? $this->visit($o->top) : null),
            ($o->set_quantifier  ? $this->visit($o->set_quantifier) : null),
            (empty($o->projections) ? '*' : implode(', ', $this->visit_each($o->projections))),
            ($o->source  ? $this->visit($o->source) : null),
            (empty($o->wheres) ? null : 'WHERE (' . implode(') AND (', $this->visit_each($o->wheres)).')'),
            (empty($o->groups) ? null : 'GROUP BY ' . implode(', ', $this->visit_each($o->groups))),
            ($o->having ? $this->visit($o->having) : null)
        )));
    }
    
    protected function visit_Qurel_Nodes_Bin(\Qurel\Nodes\Bin $o)
    {
        return $this->visit($o->expr);
    }
    
    protected function visit_Qurel_Nodes__True(\Qurel\Nodes\_True $o)
    {
        return 'TRUE';
    }
    
    protected function visit_Qurel_Nodes__False(\Qurel\Nodes\_False $o)
    {
        return 'FALSE';
    }
    
    /**
     * Get a DISTINCT expression
     *
     * @param \Qurel\Nodes\Distinct $o
     * 
     * @return string 
     */
    protected function visit_Qurel_Nodes_Distinct(\Qurel\Nodes\Distinct $o)
    {
        return 'DISTINCT';
    }
    
    /**
     * Get a DISTINCT ON expression
     *
     * @param \Qurel\Nodes\DistinctOn $o
     * 
     * @return string 
     */
    protected function visit_Qurel_Nodes_DistinctOn(\Qurel\Nodes\DistinctOn $o)
    {
        throw new \BadMethodCallException('DISTINCT ON not implemented for this db');
    }
    
    /**
     * Creates a WITH expression
     *
     * @param \Qurel\Nodes\With $o
     * 
     * @return string 
     */
    protected function visit_Qurel_Nodes_With(\Qurel\Nodes\With $o)
    {
        return 'WITH' . implode(', ', $this->visit_each($o->children));
    }
    
    /**
     * Creates a WITH RECURSIVE expression
     *
     * @param \Qurel\Nodes\WithRecursive $o
     * 
     * @return string 
     */
    protected function visit_Qurel_Nodes_WithRecursive(\Qurel\Nodes\WithRecursive $o)
    {
        return 'WITH RECURSIVE ' . implode(', ', $this->visit_each($o->children));
    }
    
    /**
     * Creates a HAVING Clause
     *
     * @param \Qurel\Nodes\Having $o
     * 
     * @return string 
     */
    protected function visit_Qurel_Nodes_Having(\Qurel\Nodes\Having $o)
    {
        return "HAVING {$this->visit($o->expr)}";
    }
    
    /**
     * Creates an OFFSET Clause
     *
     * @param \Qurel\Nodes\Offset $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Offset(\Qurel\Nodes\Offset $o)
    {
        return "OFFSET {$this->visit($o->expr)}";
    }
    
    /**
     * Get a LIMIT Clause
     *
     * @param \Qurel\Nodes\limit $o
     * 
     * @return string 
     */
    protected function visit_Qurel_Nodes_Limit(\Qurel\Nodes\Limit $o)
    {
        return "LIMIT {$this->visit($o->expr)}";
    }
    
    protected function visit_Qurel_Nodes_Top(\Qurel\Nodes\Top $o)
    {
        return '';
    }
    
    protected function visit_Qurel_Nodes_Lock(\Qurel\Nodes\Lock $o)
    {
        return $this->visit($o->expr);
    }
    
    /**
     * Groups the expression in parenthesis
     *
     * @param \Qurel\Nodes\Grouping $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Grouping(\Qurel\Nodes\Grouping $o)
    {
        return "({$this->visit($o->expr)})";
    }
    
    /**
     * Create an Ascending ORDER Expression
     *
     * @param \Qurel\Nodes\Ascending $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Ascending(\Qurel\Nodes\Ascending $o)
    {
        return  "{$this->visit($o->expr)} ASC";
    }
    
    /**
     * Create a Descending ORDER Expression
     *
     * @param \Qurel\Nodes\Descending $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Descending(\Qurel\Nodes\Descending $o)
    {
        return  "{$this->visit($o->expr)} DESC";
    }
    
    /**
     * @fixme Not sure if this is used
     */
    protected function visit_Qurel_Nodes_Group(\Qurel\Nodes\Group $o)
    {
        return $this->visit($o->expr);
    }
    
    /**
     * Visit a Named Function
     *
     * @param \Qurel\Nodes\NamedFunc $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_NamedFunc(\Qurel\Nodes\NamedFunc $o)
    {
        return "$o->name(" . ($o->distinct ? 'DISTINCT ' : '') . 
            implode(', ', $this->visit_each($o->expressions)) . ')' .
            ($o->alias ? " AS {$this->visit($o->alias)}" : '');
    }
    
    /**
     * Create a COUNT Expression
     *
     * @param \Qurel\Nodes\Count $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Count(\Qurel\Nodes\Count $o)
    {
        return 'COUNT(' . ($o->distinct ? 'DISTINCT ' : '') . 
            implode(', ', $this->visit_each($o->expressions)) . ')' .
            ($o->alias ? " AS {$this->visit($o->alias)}" : '');
    }
    
    /**
     * Create a SUM Expression
     *
     * @param \Qurel\Nodes\Sum $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Sum(\Qurel\Nodes\Sum $o)
    {
        return 'SUM(' . implode(', ', $this->visit_each($o->expressions)) . ')' .
            ($o->alias ? " AS {$this->visit($o->alias)}" : '');
    }
    
    /**
     * Create a MAX Expression
     *
     * @param \Qurel\Nodes\Max $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Max(\Qurel\Nodes\Max $o)
    {
        return 'MAX(' . implode(', ', $this->visit_each($o->expressions)) . ')' .
            ($o->alias ? " AS {$this->visit($o->alias)}" : '');
    }
    
    /**
     * Create a MIN Expression
     *
     * @param \Qurel\Nodes\Min $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Min(\Qurel\Nodes\Min $o)
    {
        return 'MIN(' . implode(', ', $this->visit_each($o->expressions)) . ')' .
            ($o->alias ? " AS {$this->visit($o->alias)}" : '');
    }
    
    /**
     * Create an AVG Expression
     *
     * @param \Qurel\Nodes\Avg $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Avg(\Qurel\Nodes\Avg $o)
    {
        return 'AVG(' . implode(', ', $this->visit_each($o->expressions)) . ')' .
            ($o->alias ? " AS {$this->visit($o->alias)}" : '');
    }
    
    /**
     * Create a table alias Expression (i.e. table AS something)
     *
     * @param \Qurel\Nodes\TableAlias $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_TableAlias(\Qurel\Nodes\TableAlias $o)
    {
        return "{$this->visit($o->relation)} AS $o->name";
    }
    
    /**
     * Create a BETWEEN Expression
     *
     * @param \Qurel\Nodes\Between $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Between(\Qurel\Nodes\Between $o)
    {
        return "{$this->visit($o->left)} BETWEEN {$this->visit($o->right)}";
    }
    
    /**
     * Create a '>=' Expression
     *
     * @param \Qurel\Nodes\GreaterThanOrEqual $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_GreaterThanOrEqual(\Qurel\Nodes\GreaterThanOrEqual $o)
    {
        return "{$this->visit($o->left)} >= {$this->visit($o->right)}";
    }
    
    /**
     * Create a '>' Expression
     *
     * @param \Qurel\Nodes\GreaterThan $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_GreaterThan(\Qurel\Nodes\GreaterThan $o)
    {
        return "{$this->visit($o->left)} > {$this->visit($o->right)}";
    }
    
    /**
     * Create a '<=' Expression
     *
     * @param \Qurel\Nodes\LessThanOrEqual $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_LessThanOrEqual(\Qurel\Nodes\LessThanOrEqual $o)
    {
        return "{$this->visit($o->left)} <= {$this->visit($o->right)}";
    }
    
    /**
     * Create a '<' Expression
     *
     * @param \Qurel\Nodes\LessThan $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_LessThan(\Qurel\Nodes\LessThan $o)
    {
        return "{$this->visit($o->left)} < {$this->visit($o->right)}";
    }
    
    /**
     * Create a LIKE Expression
     *
     * @param \Qurel\Nodes\Matches $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Matches(\Qurel\Nodes\Matches $o)
    {
        return "{$this->visit($o->left)} LIKE {$this->visit($o->right)}";
    }
    
    /**
     * Create a NOT LIKE Expression
     *
     * @param \Qurel\Nodes\DoesNotMatch $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_DoesNotMatch(\Qurel\Nodes\DoesNotMatch $o)
    {
        return "{$this->visit($o->left)} NOT LIKE {$this->visit($o->right)}";
    }
    
    /**
     * Create a FROM Expression
     *
     * @param \Qurel\Nodes\JoinSource $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_JoinSource(\Qurel\Nodes\JoinSource $o)
    {
        if (!$o->left && empty($o->right)) {
            return;
        }
        
        return "FROM " . ($o->left ? $this->visit($o->left) : null) .
            ($o->right ? ' ' . implode(' ', $this->visit_each($o->right)) : null);
    }
    
    /**
     * Create a join string
     *
     * @param \Qurel\Nodes\StringJoin $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_StringJoin(\Qurel\Nodes\StringJoin $o)
    {
        return "{$this->visit($o->left)} {$this->visit($o->right)}";
    }
    
    /**
     * Create a LEFT OUTER JOIN Expression
     *
     * @param \Qurel\Nodes\OuterJoin $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_OuterJoin(\Qurel\Nodes\OuterJoin $o)
    {
        return "LEFT OUTER JOIN {$this->visit($o->left)} {$this->visit($o->right)}";
    }
    
    /**
     * Create a INNER JOIN Expression
     *
     * @param \Qurel\Nodes\InnerJoin $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_InnerJoin(\Qurel\Nodes\InnerJoin $o)
    {
        return "INNER JOIN {$this->visit($o->left)} " . ($o->right ? $this->visit($o->right) : null);
    }
    
    /**
     * Create an ON Expression
     *
     * @param \Qurel\Nodes\On $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_On(\Qurel\Nodes\On $o)
    {
        return "ON {$this->visit($o->expr)}";
    }
    
    /**
     * Create a NOT Expression
     *
     * @param \Qurel\Nodes\Not $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Not(\Qurel\Nodes\Not $o)
    {
        return "NOT ({$this->visit($o->expr)})";
    }
    
    /**
     * Create a USING Expression
     *
     * @param \Qurel\Nodes\Using $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Using(\Qurel\Nodes\Using $o)
    {
        return "USING {$this->visit($o->expr)}";
    }
    
    /**
     * Create a UNION Expression
     *
     * @param \Qurel\Nodes\Union $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Union(\Qurel\Nodes\Union $o)
    {
        return "( {$this->visit($o->left)} UNION {$this->visit($o->right)} )";
    }
    
    /**
     * Create a UNION ALL Expression
     *
     * @param \Qurel\Nodes\UnionAll $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_UnionAll(\Qurel\Nodes\UnionAll $o)
    {
        return "( {$this->visit($o->left)} UNION ALL {$this->visit($o->right)} )";
    }
    
    /**
     * Create an INTERSECT Expression
     *
     * @param \Qurel\Nodes\Intersect $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Intersect(\Qurel\Nodes\Intersect $o)
    {
        return "( {$this->visit($o->left)} INTERSECT {$this->visit($o->right)} )";
    }
    
    /**
     * Create an EXCEPT Expression
     *
     * @param \Qurel\Nodes\Except $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Except(\Qurel\Nodes\Except $o)
    {
        return "( {$this->visit($o->left)} EXCEPT {$this->visit($o->right)} )";
    }
    
    /**
     * Visit a Qurel Table
     *
     * @param \Qurel\Table $o
     * 
     * @return string
     */
    protected function visit_Qurel_Table(\Qurel\Table $o)
    {
        return $o->table_alias ? "$o->name $o->table_alias" : $o->name;
    }
    
    /**
     * Create an IN Expression
     *
     * @param \Qurel\Nodes\In $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_In(\Qurel\Nodes\In $o)
    {
        return "{$this->visit($o->left)} IN ({$this->visit($o->right)})";
    }
    
    /**
     * Create a NOT IN Expression
     *
     * @param \Qurel\Nodes\NotIn $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_NotIn(\Qurel\Nodes\NotIn $o)
    {
        return "{$this->visit($o->left)} NOT IN ({$this->visit($o->right)})";
    }
    
    //    protected function visit_Qurel_Nodes_Any(\Qurel\Nodes\Any $o)
//    {
//        return "{$this->visit($o->left)} ANY ({$this->visit($o->right)})";
//    }
//    
//    protected function visit_Qurel_Nodes_NotInAny(\Qurel\Nodes\NotInAny $o)
//    {
//        return "{$this->visit($o->left)} <> ANY ({$this->visit($o->right)})";
//    }
//    
//    protected function visit_Qurel_Nodes_All(\Qurel\Nodes\All $o)
//    {
//        return "{$this->visit($o->left)} ALL ({$this->visit($o->right)})";
//    }
//    
//    // The word SOME is an alias for ANY.
//    protected function visit_Qurel_Nodes_Some(\Qurel\Nodes\Some $o)
//    {
//        return "{$this->visit($o->left)} SOME ({$this->visit($o->right)})";
//    }
    
    /**
     * Create an AND Expression
     *
     * @param \Qurel\Nodes\_And $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes__And(\Qurel\Nodes\_And $o)
    {
        return implode(' AND ', $this->visit_each($o->children));
    }
    
    /**
     * Create an OR Expression
     *
     * @param \Qurel\Nodes\_Or $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes__Or(\Qurel\Nodes\_Or $o)
    {
        return "{$this->visit($o->left)} OR {$this->visit($o->right)}";
    }
    
    /**
     * Handle an Assignment, join with "="
     *
     * @param  Node\Assignment $assign
     * @return string
     */
    protected function visit_Qurel_Nodes_Assignment(\Qurel\Nodes\Assignment $o)
    {
        return "{$this->visit($o->left)} = {$this->visit($o->right)}";
    }
    
    /**
     * Transform to SQL Equality, if the right operand is NULL,
     * then an "IS NULL" comparison is generated.
     *
     * @param \Qurel\Node\Equality $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_Equality(\Qurel\Nodes\Equality $o)
    {
        return ($o->right === null) ? 
                "{$this->visit($o->left)} IS NULL" : 
                "{$this->visit($o->left)} = {$this->visit($o->right)}";
    }
    
    /**
     * Transform to SQL not equal, if the right operand is NULL,
     * then an "IS NOT NULL" comparison is generated.
     *
     * @param \Qurel\Node\NotEqual $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_NotEqual(\Qurel\Nodes\NotEqual $o)
    {
        return ($o->right === null) ? 
                "{$this->visit($o->left)} IS NOT NULL" : 
                "{$this->visit($o->left)} != {$this->visit($o->right)}";
    }
    
    /**
     * Create an AS Expression
     *
     * @param \Qurel\Nodes\_As $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes__As(\Qurel\Nodes\_As $o)
    {
        return "{$this->visit($o->left)} AS {$this->visit($o->right)}";
    }

    /**
     * Returns the SQL for an Unqualified Column
     *
     * @param \Qurel\Nodes\UnqualifiedColumn $o
     * 
     * @return string
     */
    protected function visit_Qurel_Nodes_UnqualifiedColumn(\Qurel\Nodes\UnqualifiedColumn $o)
    {
        return $o->name;
    }
    
    /**
     * Returns the Attribute's fully qualified name
     *
     * @param  \Qurel\Attributes\Attribute $o
     * 
     * @return string
     */
    protected function visit_Qurel_Attributes_Attribute(\Qurel\Attributes\Attribute $o)
    {
        return (string)$o;
    }
    
    protected function visit_Qurel_Attributes_BigInteger(\Qurel\Attributes\BigInteger $o)
    {
        return $this->visit_Qurel_Attributes_Attribute($o);
    }

    protected function visit_Qurel_Attributes_Boolean(\Qurel\Attributes\Boolean $o)
    {
        return $this->visit_Qurel_Attributes_Attribute($o);
    }

    protected function visit_Qurel_Attributes_Decimal(\Qurel\Attributes\Decimal $o)
    {
        return $this->visit_Qurel_Attributes_Attribute($o);
    }

    protected function visit_Qurel_Attributes_Float(\Qurel\Attributes\Float $o)
    {
        return $this->visit_Qurel_Attributes_Attribute($o);
    }

    protected function visit_Qurel_Attributes_Integer(\Qurel\Attributes\Integer $o)
    {
        return $this->visit_Qurel_Attributes_Attribute($o);
    }

    protected function visit_Qurel_Attributes_String(\Qurel\Attributes\String $o)
    {
        return $this->visit_Qurel_Attributes_Attribute($o);
    }

    protected function visit_Qurel_Attributes_Time(\Qurel\Attributes\Time $o)
    {
        return $this->visit_Qurel_Attributes_Attribute($o);
    }

    protected function visit_Qurel_Attributes_Undefined(\Qurel\Attributes\Attribute $o)
    {
        return $this->visit_Qurel_Attributes_Attribute($o);
    }
    
    /**
     * Return the raw expression
     * 
     * @return string
     */
    protected function literal($o)
    {
        return $o;
    }
    
    /**
     * Returns a literal SQL string
     * 
     * @param \Qurel\Nodes\Sql $o
     * 
     * @return string 
     */
    protected function visit_Qurel_Nodes_Sql(\Qurel\Nodes\Sql $o)
    {
        return $this->literal($o);
    }
    
    /**
     * Returns an infix operation string
     * 
     * @param \Qurel\Nodes\InfixOperation $o
     * 
     * @return string 
     */
    protected function visit_Qurel_Nodes_InfixOperation(\Qurel\Nodes\InfixOperation $o)
    {
        return "{$this->visit($o->left)} $o->operator {$this->visit($o->right)}";
    }
    
    protected function visit_Qurel_Nodes_Addition(\Qurel\Nodes\Addition $o)
    {
        return $this->visit_Qurel_Nodes_InfixOperation($o);
    }
    
    protected function visit_Qurel_Nodes_Subtraction(\Qurel\Nodes\Subtraction $o)
    {
        return $this->visit_Qurel_Nodes_InfixOperation($o);
    }
    
    protected function visit_Qurel_Nodes_Multiplication(\Qurel\Nodes\Multiplication $o)
    {
        return $this->visit_Qurel_Nodes_InfixOperation($o);
    }
    
    protected function visit_Qurel_Nodes_Division(\Qurel\Nodes\Division $o)
    {
        return $this->visit_Qurel_Nodes_InfixOperation($o);
    }

    /**
     * Returns a quoted string
     * 
     * @return string
     */
    protected function visit_String($o)
    {
        return "'$o'";
    }
    
    /**
     * Returns an integer
     * 
     * @param int $o
     * 
     * @return int 
     */
    protected function visit_Integer($o)
    {
        return (int)$o;
    }

    /**
     * Returns a float
     * 
     * @param float $o
     * 
     * @return float 
     */
    protected function visit_Float($o)
    {
        return (float)$o;
    }
    
    /**
     * Visits an array
     * 
     * @param array $o
     * 
     * @return string 
     */
    protected function visit_Array(array $o)
    {
        return empty($o) ? 'NULL' : implode(', ', $this->visit_each($o));
    }

    /**
     * Visits an ArrayObject object
     * 
     * @param ArrayObject $o
     * 
     * @return string 
     */
    protected function visit_ArrayObject(\ArrayObject $node)
    {
        return $this->visit_Array((array)$node);
    }

    /**
     * Visits a DateTime object
     * 
     * @param DateTime $o
     * 
     * @return string 
     */
    protected function visit_DateTime(\DateTime $o)
    {
        return $this->visit($o->format('Y-m-d H:i:s'));
    }
    
    /**
     * Builds a sub-select query for an update statement
     * 
     * @param string                       $key
     * @param \Qurel\Nodes\UpdateStatement $o
     * 
     * @return \Qurel\Nodes\SelectStatement 
     */
    private function _build_subselect($key, \Qurel\Nodes\UpdateStatement $o)
    {
        $stmt              = new \Qurel\Nodes\SelectStatement;
        $core              = reset($stmt->cores);
        $core->froms       = $o->relation;
        $core->wheres      = $o->wheres;
        $core->projections = array($key);
        $stmt->limit       = $o->limit;
        $stmt->orders      = $o->orders;
        return $stmt;
    }
}