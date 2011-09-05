<?php
namespace Qurel\Nodes;

/**
 * Ascending order Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class Ascending extends Ordering
{
    public function reverse()
    {
        return new Descending($this->expr);
    }
    
    public function direction()
    {
        return 'ASC';
    }
    
    public function is_ascending()
    {
        return true;
    }
    
    public function is_descending()
    {
        return false;
    }
}