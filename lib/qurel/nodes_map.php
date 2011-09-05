<?php
// Prevent pollution from/to global namespace
call_user_func(function() {
    static $evaled = false;
    if ($evaled) {
        return;
    }
    
    // These are all the classes that extend a Node abstract class and dont really have any
    // extra methods or properties
    $binary_classes = array(
        '_As',
        '_Or',
        'Assignment',
        'Between',
        'DoesNotMatch',
        'GreaterThan',
        'GreaterThanOrEqual',
        'Join',
        'LessThan',
        'LessThanOrEqual',
        'Matches',
        'NotEqual',
        'NotIn',
        'Union',
        'UnionAll',
        'Intersect',
        'Except',
    );
    
    $unary_classes = array(
        'Bin',
        'Group',
        'Grouping',
        'Having',
        'Limit',
        'Lock',
        'Not',
        'Offset',
        'On',
        'Ordering',
        'Top',
        'DistinctOn',
        'Using'
    );
    
    $func_classes = array(
        'Sum',
        'Exists',
        'Max',
        'Min',
        'Avg',
    );
    
    $join_classes = array(
        'InnerJoin',
        'OuterJoin'
    );
    
    // Lets build the string to create the classes so we dont
    // have to call eval() inside a loop
    $append = function(array $classes, $extends) {
        static $string = '';
        foreach ($classes as $class) {
            $string .= "\nclass $class extends $extends {}\n";
        }
        return $string;
    };
    
    $append($binary_classes, 'Binary');
    $append($unary_classes , 'Unary');
    $append($func_classes , 'Func');
    $string = $append($join_classes , 'Join');
    
    eval("namespace Qurel\Nodes; $string");
    $evaled = true;
});