<?php

use Model\DB\Connection;

require_once "DB/Connection.php";

class User extends Connection {

    public function getUsers() {

        $query = $this->getCon()->query("SELECT * FROM Erabiltzaileak");
        
        $users = [];

        while($row = $query->fetch_assoc()) {

            $users[] = $row;

        }

        $query->close();

        return $users;

    }
}
?>