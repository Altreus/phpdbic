<?php

require_once dirname(__FILE__) . "/schema.class.php";

class Record {
    private $data;
    private $schema;
    private $td;
    private $loaded = false;

    public function __construct( $schema, $td, $id = NULL ) {
        $this->td = $td;
        $this->schema = $schema;

        # TODO: the id col could be an array of PK cols, defined by $td.
        $this->id = $id;
    }

    public function load() {
        # TODO: check for all PKs, not just id, since it may not be called id.
        # also, pk_col will in future return an array in some cases.
        #
        # Note: use $this->data directly to avoid recursion based on $this->loaded
        if( !empty($this->data['id']) ){
            $q = sprintf("SELECT * FROM `%s` WHERE `%s` = ?", $this->td->table_name(), $this->td->pk_col());
            $this->data = $this->schema->query($q, array($this->data['id']), PDO::FETCH_ASSOC);
            $this->data = $this->data[0];

            $this->loaded = true;
        }
    }

    public function save() {
        # Check we have the necessary data before proceeding.
        foreach ($this->td->columns() as $col) {
            $coldata = $this->td->column($col);
            assert( $coldata['is_nullable'] || !empty($this->data[$col]) );
        }

        $query = "INSERT INTO `%s` (%s) VALUES (%s)";
        $query_cols = array();
        $query_vals = array();

        foreach ($this->td->columns() as $col) {
            $coldata = $this->td->column($col);

            #TODO: implement this.
            # if ($coldata['is_ai']) continue;

            $query_cols[] = "`$col`";
            $query_vals[] = $this->data[$col];
        }

        # TODO: This will devolve into the query class eventually. 
        $query = sprintf($query, 
                         $this->td->table_name(), 
                         join(",", $query_cols), 
                         join(",", array_pad(array(), count($query_vals), "?")));
        $this->schema->query($query, $query_vals);

    }

    public function delete() {
        # TODO: use pk, not 'id'
        if( array_key_exists('id', $this->data) ) {
            assert( isset( $this->data['id'] ) );
            # TODO: pk col stuff.
            $query = sprintf("DELETE FROM `%s` WHERE `%s` = ?", $this->td->table_name(), 'id');

            $this->schema->query($query, array($this->data['id']));
            return true;
        }
    }

    public function data() {
        if( ! $this->loaded ) $this->load();
        return array_merge( $this->data );
    }

    public function __get( $prop ) {
        return $this->get($prop);
    }

    public function get( $prop ) {
        assert( $this->td->has_column( $prop ) );

        # Dude we are so, like, lazy.
        if( ! $this->loaded ) $this->load();
        if( ! array_key_exists( $prop, $this->data ) ) $this->load();

        return $this->data[$prop];
    }

    public function __set( $prop, $val ) {
        $this->set($prop, $val);
    }

    public function set( $prop, $val ) {
        assert( $this->td->has_column($prop) );

        $this->data[$prop] = $val;
    }
}
