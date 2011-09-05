<?php
namespace Qurel;

/**
 * Abstract base class for all AST managers.
 * 
 * @package    Qurel
 * @author     Andrew Wayne <lifeandcoding@gmail.com>
 * @license    MIT License
 * @copyright  2011 (c) Andrew Wayne
 * @version    0.3
 */
abstract class TreeManager
{
    protected $ast;
    protected $ctx;
    protected $engine;
    protected $visitor;
    
    protected $visitors = array(
        'sql'             => 'Qurel\Visitors\SQL',
        'mysql'           => 'Qurel\Visitors\MySQL',
        'sqlite'          => 'Qurel\Visitors\SQLite',
        # TODO!
//        'mssql'           => 'Qurel\Visitors\MSSQL',
        'postgresql'      => 'Qurel\Visitors\PostgreSQL',
//        'oracle_enhanced' => 'Qurel\Visitors\Oracle',
//      'ibm_db'          => 'Qurel\Visitors\IBM_DB',
//      'informix'        => 'Qurel\Visitors\Informix',
    );
    
    /**
     * @todo Add multiple engine support
     */
    public function __construct($engine)
    {
        $engine = strtolower($engine);
        if (!array_key_exists($engine, $this->visitors)) {
            throw new \InvalidArgumentException(sprintf(
                    "The visitor for engine: '%s', is not supported",
                    $engine
            ));
        }
        
        require_once "visitors/$engine.php";
        
        $visitor = $this->visitors[$engine];
        $this->visitor = new $visitor($engine);
        $this->engine  = $engine;
    }

    /**
     * Return the SQL string if the manager is converted to a string
     * 
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->to_sql();
        } catch (\Exception $e) {
            trigger_error('[Qurel\TreeManager] ' . $e->getMessage(), E_USER_WARNING);
        }
    }

    /**
     * Triggers the generation of an SQL string
     *
     * $param  bool   $make_human_readable Whether to return a human-readable sql formatted string
     * 
     * @return string
     */
    public function to_sql($make_human_readable = false)
    {
        $sql = $this->ast->accept($this->visitor);
        return $make_human_readable ? $this->_format_sql($sql) : $sql;
    }
    
    public function where()
    {
        foreach (func_get_args() as $expr) {
            if ($expr instanceof $this) {
                $expr = $expr->ast;
            }
            $this->ctx->wheres[] = $expr;
        }
        return $this;
    }
    
    private function _format_sql($sql, $tab_length = 4) {
        return preg_replace(
           '/(select|from|(left |right |natural |inner |outer |cross |straight_)*join|where|order by|limit|offset|update|set|insert|values)/i',
            "\n$1\n" . str_repeat(' ', $tab_length),  preg_replace('/\s+/', ' ', (string)$sql)
        );
    }
}
