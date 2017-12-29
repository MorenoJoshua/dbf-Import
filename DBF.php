<?php

class DBF
{
    public $count;
    public $rows;
    public $fields;
    public $records;
    private $dbf;

    public function __construct($dbfFile)
    {
        if (file_exists($dbfFile)) {
            $this->dbf = $dbfFile;
            $this->__parse();
            return true;
        } else {
            echo 'error: file does not exsist';
        }
    }

    private function __parse()
    {
        $fdbf = fopen($this->dbf, 'r');
        $fields = array();
        $buf = fread($fdbf, 32);
        $header = unpack("VRecordCount/vFirstRecord/vRecordLength", substr($buf, 4, 8));
        $this->count = $header['RecordCount'];
        $goon = true;
        $unpackString = '';
        while ($goon && !feof($fdbf)) { // read fields:
            $buf = fread($fdbf, 32);
            if (substr($buf, 0, 1) == chr(13)) {
                $goon = false;
            } // end of field list
            else {
                $field = unpack("a11fieldname/A1fieldtype/Voffset/Cfieldlen/Cfielddec", substr($buf, 0, 18));
//                echo 'Field: ' . json_encode($field) . '<br/>';
                $unpackString .= "A$field[fieldlen]$field[fieldname]/";
                array_push($fields, $field);
            }
        }
        $this->fields = $fields;

//        $this->parseFields($fdbf, $header, $unpackString);

        fclose($fdbf);

    }

    public function createQuery($dbname)
    {
//        create database $dbname if not exists;
//        create table $dbfname if not exixsts (field type,)

        $createDb = <<<SQL
CREATE database if not exists $dbname
SQL;

        foreach ($this->fields as $field) {
            $name = strtolower(substr($field['fieldname'], 0, -2));
            $type = $this->__getType($field['fieldtype'], $field['fieldlen']);
            $fields[] = [
                'name' => $name,
                'type' => $type,
                'lenght' => $field['fieldlen'],
                'dec' => $field['fielddec']
            ];
            $toquery[] = "$name $type";
        }

        $tableFields = join(', ', $toquery);
        $tablename = substr(basename($this->dbf, PATHINFO_BASENAME),0 , strpos(basename($this->dbf, PATHINFO_BASENAME), '.'));

        return $createTable = <<<SQL
        $createDb;
create table if not EXISTS $dbname.$tablename ($tableFields);

SQL;


    }

    private function __getType($fieldtype, $fieldlenght)
    {
        switch ($fieldtype) {
            case 'C':
                return "varchar($fieldlenght)";
                break;
            case 'F':
                return "float";
                break;
            case 'L':
                return "varchar($fieldlenght)";
                break;
            case 'M':
                return "varchar($fieldlenght)";
                break;
            case 'N':
                return "bigint";
                break;
            default:
                return 'varchar(64)';
        }
    }

    private function __fieldTypes()
    {
        return [
            'C' => 'varchar', // All ASCII characters (padded with whitespaces up to the field's length)
            'D' => 'date', // Numbers and a character to separate month, day, and year (stored internally as 8 digits in YYYYMMDD format)
            'F' => 'float', //-.0123456789 (right justified, padded with whitespaces)
            'L' => 'logical', //YyNnTtFf? (? when not initialized)
            'M' => 'memo', //All ASCII characters (stored internally as 10 digits representing a .dbt block number, right justified, padded with whitespaces)
            'N' => 'numeric' //-.0123456789 (right justified, padded with whitespaces)
        ];
    }

    private function parseFields($fdbf, $header, $unpackString)
    {
        fseek($fdbf, $header['FirstRecord'] + 1); // move back to the start of the first record (after the field definitions)
        $records = [];
        $l = 0;
        $packet = 1000;
        for ($i = 1; $i <= $header['RecordCount']; $i++) {

            if ($i % $packet == 0) {
                $out = fopen('./jsons/' . $i . '.json', 'w');
                fwrite($out, json_encode($records));
                $records = [];
                echo $i . "\r\n";
            }
            $buf = fread($fdbf, $header['RecordLength']);
            $record = array_filter(array_map('trim', unpack($unpackString, $buf)));
            $records[] = $record;
//            echo 'record: ' . json_encode($record) . '<br/>';
//            echo $i . $buf . '<br/>';
        } //raw record
//        $this->records = $records;

    }

}