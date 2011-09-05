<?php
namespace Qurel\Visitors;

/**
 * Abstract Visitor: Base class for all visitors
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
abstract class Visitor
{
    public function accept($object)
    {
        return $this->visit($object);
    }
    
    public function dispatch($klass)
    {
        static $array = array(); // persistent
        $klass  = is_object($klass) ? get_class($klass) : ucfirst(gettype($klass));
        $klass  = str_replace(array('\\'), '_', $klass);
        return isset($array[$klass]) ? $array[$klass] : $array[$klass] = "visit_$klass";
    }
    
    /**
     * Inspects the Node and calls the appropiate
     * type-specific Visitor if possible.
     *
     * For example: 
     *   - If the Node is of Class "Qurel\\Node\\Equality", then the
     *     concrete visitor method "visit_Qurel_Node_Equality" gets called
     * 
     * The visitor method always receives the node, unchanged, as first argument.
     * You may enforce more strict typing in the more specific visitor methods.
     *
     * @param  object $object The object, an instance of \Qurel\Node
     * 
     * @return mixed The return value of the concrete visitor
     * 
     * @throws \BadMethodCallException If no visitor method for the class is found
     */
    public function visit($object)
    {
        $method_name = $this->dispatch($object);
        $method = array($this, $method_name);
        if (!is_callable($method)) {
            $visiting = is_object($object) ? get_class($object) : gettype($object);
            throw new \BadMethodCallException("Cannot visit $visiting");
        }
        
        return call_user_func($method, $object);
    }
    
    /**
     * Utility Method for visiting each item in an list
     *
     * @param  array $list
     * 
     * @return array The result of each visit
     */
    protected function visit_each($list)
    {
        return array_map(array($this, 'visit'), (array)$list);
    }
}