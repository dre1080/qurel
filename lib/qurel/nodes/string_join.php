<?php
namespace Qurel\Nodes;

/**
 * StringJoin Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class StringJoin extends Join
{
    public function __construct($left, $right = null)
    {
        parent::__construct(new \Qurel\Table($left), $right);
    }
}