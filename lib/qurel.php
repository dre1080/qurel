<?php
require_once 'qurel/table.php';
require_once 'qurel/attributes.php';

require_once 'qurel/visitors.php';

require_once 'qurel/tree_manager.php';
require_once 'qurel/insert_manager.php';
require_once 'qurel/select_manager.php';
require_once 'qurel/update_manager.php';
require_once 'qurel/delete_manager.php';
require_once 'qurel/nodes.php';

// Convenience Alias
class_alias('Qurel\Nodes\Node', 'Node');

/**
 * Qurel
 * 
 * @link http://magicscalingsprinkles.wordpress.com/2010/01/28/why-i-wrote-arel/
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class Qurel
{
    /**
     * Version, for use with version_compare
     *
     * @const string
     */
    const VERSION = '0.3.0';
    
    /**
     * Marks the provided raw SQL as safe, by wrapping it
     * inside an SqlLiteral Instance
     *
     * @param  string $raw_sql The string of raw SQL, which should be marked as safe
     * 
     * @return Qurel\Nodes\Sql
     */
    public static function sql($raw_sql)
    {
        return new Qurel\Nodes\Sql($raw_sql);
    }
    
    /**
     * Returns the '*' quantifier wrapped as SQL Literal
     * for use in projections
     *
     * @return object Qurel\Nodes\SqlLiteral
     */
    public static function star()
    {
        return static::sql('*');
    }
}