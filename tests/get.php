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

function test_fetch_all() {
    $db = new TestSchema();

    $records = $db->resultset('TestTable')->all();

    if( !is_array($records) ) {
        return PEAR::raiseError("all() did not return array");
    } 
    if( count($records) != 2) {
        return PEAR::raiseError("find() did not return 2 results: " . count($records) . " found");
    }
    if( $records[0]->id != 1 ) {
        return PEAR::raiseError("find() did not find id=1 in pos [0]: " . $records[0]->id . " found");
    }
    if( $records[1]->id != 2 ) {
        return PEAR::raiseError("find() did not find id=2 in pos [1]: " . $records[1]->id . " found");
    }
    if( $records[0]->charfield != "Test Title" ) {
        return PEAR::raiseError("Record [0] was not correctly constructed");
    }
    if( $records[1]->charfield != "Test Title 2" ) {
        return PEAR::raiseError("Record [1] was not correctly constructed");
    }

    return true;
}

# Test bug where ->data() did not load()
function test_get_data() {
    $db = new TestSchema();

    $records = $db->resultset('TestTable')->all();
    $rec = $records[0];

    $data = $rec->data();

    if( !is_array($data) ){
        return PEAR::raiseError("data() did not return array");
    }
    if( !array_key_exists('charfield', $data) ) {
        return PEAR::raiseError("data() did not return any data");
    }

    return true;
}

Testing::runTests(array(
    "test_fetch_by_id",
    "test_fetch_all",
    "test_get_data",
));
