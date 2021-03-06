<?php

require_once dirname(__FILE__) . "/schema.class.php";
require_once dirname(__FILE__) . "/tabledef.class.php";

class ResultSet {
    private $records;
    private $schema;

    # array 0 = query string 1 = bind vars
    private $query;
    private $td;

    public function __construct( Schema $s, TableDef $td ) {
        $this->schema = $s;
        $this->td = $td;
        $this->records = array();

        # TODO: query class
        $this->query = array(sprintf( "SELECT `%s` FROM `%s`", $td->pk_col(), $td->table_name() ), array());
    }

    public function find( $id ) {
        $pk_col = $this->td->pk_col();

        $query = " WHERE `%s` = ?";
        $this->query[0] .= sprintf( $query, $pk_col );
        $this->query[1][] = $id;

        # YAGNI
#        if( is_array( $pk_col )) {
#            assert( is_array( $id ));
#
#            foreach( $pk_col as $col_id ) {
#                assert( isset($id[$col_id]) );
#        }
        return $this;
    }

    public function search( $search_params ) {
        $this->query[0] .= " WHERE ";
        $cond = array();

        foreach ($search_params as $col => $criteria) {
            $cond[] = " `$col` = ? ";

            # TODO: Criteria is a plural because we need to construct a query with potentially
            # more than one criterion in it. For now it is just a straight a = b thing.
            $this->query[1][] = $criteria;
        }

        $this->query[0] .= join(" AND ", $cond);
        return $this;
    }

    public function all() {
        $ids = $this->schema->query($this->query[0], $this->query[1] );#PDO::FETCH_NUM);

        foreach($ids as $id) {
            $id = $id['id']; # PDO returns strange things...
            array_push($this->records, new Record($this->schema, $this->td, $id));
        };

        return $this->records;
    }

    public function single() {
        $this->all();
        return $this->records[0];
    }

    public function create( $data ) {
        $record = new Record( $this->schema, $this->td );

        foreach ($data as $col => $val) {
            $record->$col = $val;
        }

        $record->save();
        return $record;
    }
}
