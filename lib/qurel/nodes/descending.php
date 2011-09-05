<?php
namespace Qurel\Nodes;

/**
 * Descending order Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class Descending extends Ordering
{
    public function reverse()
    {
        return new Ascending($this->expr);
    }
    
    public function direction()
    {
        return 'DESC';
    }
    
    public function is_ascending()
    {
        return false;
    }
    
    public function is_descending()
    {
        return true;
    }
}