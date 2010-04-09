<?php

require_once(dirname(__FILE__) . "/testing.class.php");
require_once(dirname(__FILE__) . "/../phpdbic.php");
require_once(dirname(__FILE__) . "/schema/testtable.class.php");

class TestSchema extends Schema {
    public function __construct() {
        parent::__construct("sqlite:test-get.db");
        $this->register_resultset("TestTable");
    }
}

function test_fetch_by_id() {
    $db = new TestSchema();

    $rs = $db->resultset('TestTable')->find(1);

    $records = $rs->all();

    if( !is_array($records) ) {
        return PEAR::raiseError("all() did not return array");
    } 
    if( count($records) != 1) {
        return PEAR::raiseError("find() did not return 1 result: " . count($records) . " found");
    }
    if( $records[0]->id != 1 ) {
        return PEAR::raiseError("find() did not find id=1: " . $records[0]->id . " found");
    }
    if( $records[0]->charfield != "Test Title" ) {
        return PEAR::raiseError("Record was not correctly constructed");
    }

    return true;
}

Testing::runTests(array(
    "test_fetch_by_id",
));