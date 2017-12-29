<?php

class DBFunc
{

    private $db;

    function __construct($dbfFile)
    {
        require '../xbase.php';

        $this->db = new \XBase\Table($dbfFile);

    }


    function createTableQueryFromDFB($table)
    {
//NOTE:
//types:
//C = character
//N = !decimalCount ? int : float
//D = date (Wed, 01 Jun 2016 00:00:00 -0700)
//L = bool

        $l = 0;
        foreach ($this->db->getColumns() as $key => $val) {
            echo 'Loop ' . $l . "\r\n";
            $l++;
            $cols[] = $key;
            $i = $val->type;
            if ($i == 'C') {
                $type = "VARCHAR({$val->length})";
            } elseif ($i == 'N') {
                if ($val->decimalCount == 0) {
                    $type = 'INT';
                } else {
                    $type = 'FLOAT';
                }
            } elseif ($i == 'L') {
                $type = 'INT'; // Tal ves hay algo mas
            } elseif ($i == 'D') {
                $type = 'DATETIME';
            } else {
            }

            @$fields[] = <<<text
{$val->name} $type
text;

        }

        $fieldInput = join(", \r\n", $fields);
        return <<<SQL
CREATE TABLE If NOT EXISTS $table
(
$fieldInput
);
SQL;
    }

    function getAsArray()
    {

        foreach ($this->db->getColumns() as $key => $val) {
            $cols[] = $key;
        }
        $arrayfile = [];

        $i = 0;
        while ($row = $this->db->nextRecord()) {
            echo "Loop $i\r\n";
            $i++;
            $thisrow = [];
            $sub = 0;
            foreach ($cols as $col) {
                $thisrow[$sub][$col] = $row->$col;
                if ($i % 1000 == 0) {
                    $sub++;
                }
            }
            $arrayfile[] = $thisrow;
        }

        return $arrayfile;
    }

    function toTimestamp(&$epoch)
    {
        $epoch = date('Y-m-d H:i:s', strtotime($epoch));
    }

}