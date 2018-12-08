<?php
/**
 * Created by PhpStorm.
 * User: Nakmak
 * Date: 01.12.2018
 * Time: 23:42
 */

namespace Logic;
require_once 'Db/Db.php';

class BasicAuth extends AuthenticateUser {

    protected $dbconn;
    protected $login;
    protected $password;
    protected $user;

    function __construct($params) {
        parent::__construct();
        $this->dbconn = \Db::getConnection();
        $this->login = $params['email'];
        $this->password = $params['password'];
    }

    public function authenticate() {
        $sql = "SELECT id, password FROM users WHERE email='$this->login'";
        $results = $this->dbconn->query($sql);
        $this->user = $results->fetch_assoc();
        if (($results->num_rows) and (password_verify($this->password, $this->user['password']))) {
            return true;
        }
        return false;
    }

    public function login() {
        session_start();
        $sessid = session_id();
        $id = $this->user['id'];
        $sql = "UPDATE user_data SET session_id='$sessid' WHERE user_id='$id'";
        $this->dbconn->query($sql);
        setcookie("sessid", $sessid, time() + (86400 * 30), "/");
    }

    public function logout() {
        session_abort();
        $sessid = $_COOKIE['sessid'];
        $sql = "DELETE FROM gamesite.user_data
                WHERE gamesite.user_data.session_id = '$sessid'";
        $this->dbconn->query($sql);
        unset($_COOKIE['sessid']);
    }
}