<?php

class Schema {
    private $db;
    private $class_register;

    public function __construct( $dsn, $user = NULL, $pass = NULL, $dopts = NULL ) {
        $class = "PDO";#__DEBUG__ ? "ProfiledPDO" : "PDO";

        $this->db = new $class( $dsn, $user, $pass, $dopts );

        $this->class_register = array();
    }

    # Presumably, if you manage to call this, your class file will already have been
    # required
    public function register_resultset( $tabledef_classname, $resultset_classname = NULL) {
        if (empty($resultset_classname)) $resultset_classname = "ResultSet";

        $this->class_register[$tabledef_classname] = $resultset_classname;
    }

    public function resultset( $classname ) {
        if( isset( $this->class_register[$classname] )){
            $td = new $classname();
            return new $this->class_register[$classname]($this, $td);
        }
        else {
            throw new Exception("$classname does not have a registered ResultSet.");
        }
    }

    public function query( $query, $bind_vars = array(), $return = PDO::FETCH_ASSOC ) {
        $stmt = $this->db->prepare($query);
        $stmt->execute( $bind_vars );
        $results = $stmt->fetchAll( $return );

        if( count($results) == 1 ) return $results[0];

        return $results;
    }

    public function prepare( $query ) {
        return $this->db->prepare( $query );
    }
}
