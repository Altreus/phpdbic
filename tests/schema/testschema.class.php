<?php

class TestSchema extends Schema {
    public function __constrtuct() {
        parent::__construct("sqlite:" . dirname(__FILE__) . "/test.db");
    }
}
