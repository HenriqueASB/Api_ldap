<?php

class LDAP {
    private $conn;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->conn = ldap_connect(getenv('LDAP_SERVER'), getenv('LDAP_PORT')) or die("Erro ao criar conexão LDAP!");
    }

    public function authenticate($username, $password) {
        if (!ldap_bind($this->conn, getenv('LDAP_DOMAIN') . '\\' . $username, $password)) {
            return FALSE;
        } else {
            $bind= ldap_bind($this->conn, getenv('LDAP_DOMAIN') . '\\' . $username, $password);
            return $this->getUser($username);
        }
    }

    private function getUser($username) {
        $filter = "(" . getenv('LDAP_USERNAME_FIELD') . "=".$username.")";
        $result = ldap_search($this->conn, getenv('LDAP_BASE_DN'), $filter);
        $re = get_resource_type($result);
        echo $re;

        if(!$result) {
            return FALSE;
        } else {
            $info = ldap_get_entries($this->conn, $result);

            if (count($info) < 1) {
               return FALSE;
            } else {
                $user = array();
                $user['username'] = $username;
                $user['fullName'] = $info[0]["cn"][0];
                (isset($info[0]["mail"]) && $user['email'] = $info[0]["mail"][0]) || $user['Email'] = NULL;

                return $user;
            }
        }
    }
}

?>
