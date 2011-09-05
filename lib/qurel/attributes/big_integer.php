<?php
namespace Qurel\Attributes;

/**
 * Unfortunately, PHP has problems with big integers. 
 * PHP will automatically convert these types of integers into scientific notation.
 * 
 * For example on windows 32-bit: 
 *     int(18446744073709551615) is converted to float(1.844674407371E+19)
 * 
 * This class is a workaround
 * 
 * @link https://bugs.php.net/bug.php?id=43053
 * @link http://www.mysqlperformanceblog.com/2008/01/10/php-vs-bigint-vs-float-conversion-caveat/
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
class BigInteger extends Attribute
{
    /**
     * The constructor.
     * 
     * @param string|int $amount
     * 
     * @return \Qurel\Attributes\BigInteger
     */
    public function __construct($amount)
    {
        // sprintf(%.0f, $s) is a workaround, You could also use number_format(18446744073709551615, 0, '.', '')
        $this->name = sprintf('%.0f', $amount);
        $this->relation = null;
    }
    
    public function __toString()
    {
        return $this->name;
    }
}