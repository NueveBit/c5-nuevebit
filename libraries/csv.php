<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Csv {

    private $handle = NULL;
    private $maxLineLength = 0;
    private $delimiter = NULL;

    public function __construct($file, $maxLineLength = 10000, $delimiter = ",") {
        $this->handle = fopen($file, "r");

        if ($this->handle === FALSE) {
            throw new Exception("invalid csv file");
        }

        $this->maxLineLength = $maxLineLength;
        $this->delimiter = $delimiter;
    }

    public function getLineArray($allowEmptySpaces = TRUE) {
        $line = fgetcsv($this->handle, $this->maxLineLength, $this->delimiter);

        if (!$allowEmptySpaces) {
            $deleteIndexes = array();
            
            for ($i = 0; $i < count($line); $i++) {
                $column = $line[$i];

                if ($column == "") {
                    $deleteIndexes[] = $i;
                }
            }

            foreach ($deleteIndexes as $index) {
                unset($line[$index]);
            }
        }

        if ($line === FALSE) {
            fclose($this->handle);
        }

        return $line;
    }
}

class CSVImporter {

    const MAX_LINE_LENGTH = 100000;

    public static function toArray($sourceFile, $delimiter = ",") {
        $d = array();

        if (($handle = fopen($sourceFile, "r")) !== false) {
//            // ignore column names
//            fgetcsv($handle, self::$max_line_length, ",");

            while (($data = fgetcsv($handle, self::MAX_LINE_LENGTH, $delimiter)) !== false) {
                $d[] = $data;
            }
        }

        return $d;
    }

    // Code adapted from php manual user: erelsgl at gmail dot com
    public static function toDatabase($source, $targetTable, $idAutoInc = true) {
        if (is_string($source)) {
            self::importFromFile($source, $targetTable, $idAutoInc);
        } else {
            self::importFromArray($source, $targetTable, $idAutoInc);
        }
    }

    private static function importFromFile($source, $targetTable, $idAutoInc) {
        $max_line_length = self::MAX_LINE_LENGTH;
        $handle = fopen($source, "r");
        
        if ($handle === false) {
            throw new Exception("Invalid source file given");
        }
        
        $columns = fgetcsv($handle, $max_line_length, ",");
        self::fixColumns($columns);
        
        while (($data = fgetcsv($handle, $max_line_length, ",")) !== FALSE) {
            self::insert($targetTable, $columns, $data, $idAutoInc);
        }
        
        fclose($handle);
    }

    private static function importFromArray($source, $targetTable, $idAutoInc) {
        $columns = $source[0];
        self::fixColumns($columns);

        for ($i = 1; $i < count($source); $i++) {
            $row = $source[$i];
            
            self::insert($targetTable, $columns, $row, $idAutoInc);
        }

    }

    private static function insert($targetTable, $columns, $row, $idAutoInc) {
        $db = Loader::db();
        
        $cols = ($idAutoInc) ? "id" : "";
        $cols .= join(",", $columns);

        $insert_query_prefix = "INSERT INTO $targetTable ($cols) VALUES";

        while (count($row) < count($columns))
            array_push($row, NULL);

        $values = ($idAutoInc) ? "NULL, " : "";
        $values .= join(",", self::quote_all_array($db, $row));

        $query = "$insert_query_prefix ($values)";
        $db->Execute($query);
    }

    private static function fixColumns($columns) {
        foreach ($columns as &$column) {
            $column = str_replace(".", "", $column);
        }
    }

    private static function quote_all_array($db, $values) {
        foreach ($values as $key => $value)
            if (is_array($value))
                $values[$key] = self::quote_all_array($db, $value);
            else
                $values[$key] = self::quote_all($db, $value);
        return $values;
    }

    private static function quote_all($db, $value) {
        if (is_null($value))
            return "NULL";

        $value = $db->qstr($value);
        return $value;
    }

}
?>
