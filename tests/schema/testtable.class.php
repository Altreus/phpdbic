<?php

class TestTable extends TableDef {
    public function __construct() {
        $cols = array(
            "id" => array(
                "data_type" => "integer",
                "is_pk"     => 1,
                "is_ai"     => 1,
                "is_nullable" => 0,
            ),
            "charfield" => array(
                "data_type" => "varchar",
                "size"      => 255,
                "is_nullable" => 0,
            ),
            "textfield" => array(
                "data_type" => "text",
                "is_nullable" => 0,
            ),
            "datefield" => array(
                "data_type" => "date",
                "is_nullable" => 0,
            ),
            "dtfield" => array(
                "data_type" => "datetime",
                "is_nullable" => 0,
            ),
        );

        parent::__construct('testtable', $cols);
    }
}
