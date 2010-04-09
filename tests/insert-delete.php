<?php

require_once(dirname(__FILE__) . "/testing.class.php");
require_once(dirname(__FILE__) . "/../phpdbic.php");
require_once(dirname(__FILE__) . "/schema/testtable.class.php");

class TestSchema extends Schema {
    public function __construct() {
        parent::__construct("sqlite:test-insert-delete.db");
        $this->register_resultset("TestTable");
    }
}

function test_insert() {
    $db = new TestSchema();

    $records = $db->resultset('TestTable')->all();

    if( !is_array( $records ) ) {
        return PEAR::raiseError("all() did not return array");
    } 
    if( count($records) != 0 ) {
        return PEAR::raiseError("Database is not empty - please empty it before testing.");
    }

    # TODO: let auto-increment take care of itself
    $data = array(
        "id" => 1,
        "charfield" => "Test Insert",
        "textfield" => "Test text data",
        "datefield" => "2010-04-09",
        "dtfield"   => "2010-04-09 22:25:00",
    );
    $new_rec = $db->resultset('TestTable')->create($data);

    if( !$new_rec ) {
        return PEAR::raiseError("create() did not return anything");
    }
    if( !($new_rec instanceof Record) ) {
        return PEAR::raiseError("create() did not create a record");
    }
    if( $new_rec->charfield != "Test Insert")  {
        return PEAR::raiseError("create() did not populate the record correctly");
    }

    $records = $db->resultset('TestTable')->all();

    if( count($records) != 1 ) {
        return PEAR::raiseError("create() did not seem to insert the record.");
    }
    if( $records[0]->id != 1 ) {
        return PEAR::raiseError("create() did not seem to insert the right id.");
    }

    return true;
}

function test_delete() {
    $db = new TestSchema();

    $data = array(
        "id" => 2,
        "charfield" => "Test Insert",
        "textfield" => "Test text data",
        "datefield" => "2010-04-09",
        "dtfield"   => "2010-04-09 22:25:00",
    );

    $db->resultset('TestTable')->create($data);
    $data['id'] = 3;
    $db->resultset('TestTable')->create($data);
    $data['id'] = 4;
    $db->resultset('TestTable')->create($data);

    $records = $db->resultset('TestTable')->find(3)->all();

    $records[0]->delete();

    $records = $db->resultset('TestTable')->find(3)->all();
    if( count($records) != 0 ) {
        return PEAR::raiseError("We found record 3 - so it was not deleted.");
    }

    $records = $db->resultset('TestTable')->all();

    foreach ($records as $rec) {
        $rec->delete();
    }

    $records = $db->resultset('TestTable')->all();

    if( count($records) != 0 ) {
        return PEAR::raiseError("delete() did not seem to remove the records.");
    }

    return true;
}

Testing::runTests(array(
    "test_insert", 
    "test_delete"
));
