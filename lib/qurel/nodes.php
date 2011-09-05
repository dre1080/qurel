<?php
namespace Qurel\Nodes;

const InnerJoin  = '\Qurel\Nodes\InnerJoin';
const OuterJoin  = '\Qurel\Nodes\OuterJoin';
const StringJoin = '\Qurel\Nodes\StringJoin';

// node
require_once 'nodes/node.php';
require_once 'nodes/select_statement.php';
require_once 'nodes/select_core.php';
require_once 'nodes/insert_statement.php';
require_once 'nodes/update_statement.php';

require_once 'nodes/distinct.php';
require_once 'nodes/_true.php';
require_once 'nodes/_false.php';

// unary
require_once 'nodes/unary.php';
require_once 'nodes/ascending.php';
require_once 'nodes/descending.php';
require_once 'nodes/unqualified_column.php';
require_once 'nodes/with.php';

// binary
require_once 'nodes/binary.php';
require_once 'nodes/equality.php';
require_once 'nodes/in.php'; // Why is this subclassed from equality?
require_once 'nodes/join_source.php';
require_once 'nodes/delete_statement.php';
require_once 'nodes/table_alias.php';
require_once 'nodes/infix_operation.php';

// nary
require_once 'nodes/_and.php';

// function
require_once 'nodes/func.php';
require_once 'nodes/count.php';
require_once 'nodes/values.php';
require_once 'nodes/named_func.php';

require_once 'nodes_map.php';

// joins
require_once 'nodes/string_join.php';

require_once 'nodes/sql.php';