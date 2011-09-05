<?php
namespace Qurel;

/**
 * Table: Represents a relation
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class Table implements \ArrayAccess
{
    public $name, $table_name;
    public $aliases = array();
    public $table_alias;
    public $engine;
    
    protected $attr_cache = array();

    /**
     * Constructor
     *
     * @param string $name   The table name 
     * @param string $engine The database engine type
     */
    public function __construct($name, $engine = 'mysql')
    {
        $this->name = $this->table_name = (string)$name;
        $this->engine = $engine;
    }
    
    public function alias($name = null)
    {
        $name || $name = "{$this->name}_2";
        return $this->aliases[] = new Nodes\TableAlias($this, $name);
    }

    /**
     * Returns a new Select Manager and selects this table
     * 
     * @return SelectManager
     */
    public function from($table)
    {
        return new SelectManager($this->engine, $table);
    }

    /**
     * Returns a new Select Manager and adds the given expressions as projections
     *
     * @param  array $projections|mixed $projection,...
     * 
     * @return SelectManager
     */
    public function project(/*array $projections*/)
    {
        return call_user_func_array(array($this->from($this), 'project'), func_get_args());
    }

    /**
     * Create a new Select Manager and join the given relation
     *
     * @param  mixed $relation
     * @return SelectManager
     */
    public function join($relation, $klass = \Qurel\Nodes\InnerJoin)
    {
        return $this->from($this)->join($relation, $klass);
    }

    /**
     * Create a new Select Manager and add the given expressions 
     * to its restrictions.
     *
     * @param  mixed $expr,...
     * @return SelectManager
     */
    public function where()
    {
        return call_user_func_array(array($this->from($this), 'where'), func_get_args());
    }
    
    public function filter()
    {
        return call_user_func_array(array($this->from($this), 'where'), func_get_args());
    }
    
    public function exclude()
    {
        $expr = func_get_args();
        $keys = array_keys($expr);
        $col  = array();
        foreach ($keys as $key) {
            $col[] = $this[$key]->not_in($expr[$key]);
        }
        
        return call_user_func_array(array($this->from($this), 'where'), $col);
    }

    /**
     * Create a new Select Manager and with an Order Expression
     *
     * @param  mixed  $expr      Order Expression or Sort Column
     * @param  string $direction Optional, Order Direction if an Sort Column
     *                           is given as first argument
     * @return SelectManager
     */
    public function order()
    {
        return call_user_func_array(array($this->from($this), 'order'), func_get_args());
    }

    /**
     * Create a new Select Manager and group by the given Attribute
     *
     * @return SelectManager
     */
    public function group()
    {
        return call_user_func_array(array($this->from($this), 'group'), func_get_args());
    }
    
    public function having($expr)
    {
        return $this->from($this)->having($expr);
    }

    /**
     * Create a new Select Manager and limit the number of
     * rows returned to the given amount.
     *
     * @param  int $limit
     * @return SelectManager
     */
    public function take($limit)
    {
        return $this->from($this)->take($limit);
    }

    /**
     * Create a new Select Manager and skip
     * the given amount of rows
     *
     * @param  int $amount
     * @return SelectManager
     */
    public function skip($amount)
    {
        return $this->from($this)->skip($amount);
    }
    
    /**
     * Create a LOWER() function
     * 
     * @param mixed $column 
     */
    public function lower($column)
    {
        return new Nodes\NamedFunc('LOWER', array($column));
    }
    
    public function select_manager()
    {
        return new SelectManager($this->engine);
    }

    public function insert_manager()
    {
        return new InsertManager($this->engine);
    }
    
    public function delete_manager()
    {
        return new DeleteManager($this->engine);
    }
    
    public function update_manager()
    {
        return new UpdateManager($this->engine);
    }
    
    /**
     * Allow to access Table Attributes as properties
     *
     * @return Attribute
     */
    public function __get($name)
    {
        return isset($this->attr_cache[$name]) 
              ? $this->attr_cache[$name] 
              : $this->attr_cache[$name] = new Attributes\Attribute($this, $name);
    }

    /**
     * Returns the Table Name for debugging purposes
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

    /**
     * Returns a new attribute with name $offset
     * 
     * @param string $offset
     * 
     * @return \Arel\Attributes\Attribute
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }
    
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Qurel\Table is read-only');
    }

    public function offsetExists($offset)
    {
        return isset($this->attr_cache[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->attr_cache[$offset]);
    }
}
