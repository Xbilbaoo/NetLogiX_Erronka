<?php

namespace Model\DB;

class Connection {

    protected $con;

    function __construct() {

        $this->con = new \mysqli("localhost", "root", "", "netlogix");

    }

    public function getCon() {

        return $this->con;

    }

    public function closeCon() {

        $this->con->close();

    }


}
