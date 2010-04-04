<?php

require_once dirname(__FILE__) . "/schema.class.php";

class Record {
    private $data;
    private $schema;
    private $td;

    public function __construct( $schema, $td, $id = NULL ) {
        $self->td = $td;

        if( !empty($id) ){
            $q = sprintf("SELECT * FROM `%s` WHERE `%s` = ?", $td->table_name(), $td->pk_col());
            $this->data = $schema->query($q, array($id), PDO::FETCH_ASSOC);
        }
    }

    public function get_value( $column ) {
        assert( $this->td->has_column( $column ));

        return $this->data[$column];
    }

    public function data() {
        return array_merge( $this->data );
    }
}
