<?php
namespace Qurel\Nodes;

/**
 * JoinSource Node
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class JoinSource extends Binary
{
    public function __construct($single_source, array $joinop = array())
    {
        parent::__construct($single_source, $joinop);
    }
}