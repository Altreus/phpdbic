<?php

class TableDef {
    private $columns;
    private $table_name;

    public function __construct( $tablename, $coldefs = array() ){
        assert( !empty( $tablename ) );

        $this->table_name = $tablename;
        $this->columns = array();

        foreach($coldefs as $colname => $colattrs) {
            $this->add_column($colname, $colattrs);
        }
    }

    public function add_column( $name, $attrs ) {
        $col_name = $name;

        # Create keys but no or false values for optional attributes we need later.
        foreach( array( 'is_pk' ) as $attr ) {
            if(!isset($attrs[$attr])) $attrs[$attr] = 0;
        }

        $this->columns[$col_name] = $attrs;
    }

    public function has_column( $colname ) {
        return array_key_exists( $this->columns, $colname );
    }

    public function table_name() {
        return $this->table_name;
    }

    public function pk_col() {
        $ret = array();

        foreach( $this->columns as $colname => $attrs ) {
            if( $attrs['is_pk'] ) array_push( $ret, $colname );
        }

        if( count($ret) == 1 ) return $ret[0];
        return $ret;
    }
}
