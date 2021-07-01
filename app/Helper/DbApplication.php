<?php
namespace App\Helper;

use Event;
use Exception;

class DbApplication
{
    /*
     *  \App\Helper\DbApplication::dumpQuery();
     *    
     */
    public static function dumpQuery()
    {
        try {
            Event::listen(
                'Illuminate\Database\Events\QueryExecuted',
                function ($query) {
                    $bindingary = $query->bindings;
                    $rtn = $query->sql;
                    foreach ($bindingary as $binding) {
                        $value = is_numeric($binding) ? $binding : "'" . $binding . "'";
                        $rtn = preg_replace('/\?/', $value, $rtn, 1);
                    }
                    $rtn .= ';                 CostTime =>(' . $query->time . ')/ms Database =>(' . $query->connection->getDatabaseName() . ')';
                    if (config('app.debug') === true) {
                        dump($rtn);
                    }
                }
            );
        } catch (Exception $e) {

        }
    }
}

