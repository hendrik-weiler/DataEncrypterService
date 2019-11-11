<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Authentication
{
    public static function generateHash()
    {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }

    public static function login($controller, $username, $password, $generateApiHash=false)
    {
        $sql = "SELECT id,username,hash,role FROM accounts WHERE username = ? AND password = ?";
        $query = $controller->db->query($sql, array($username, sha1($password)));
        $row = $query->row();
        if(is_object($row)) {
            $newHash = static::generateHash();
            if($generateApiHash) {
                $row->apiHash = $newHash;
                $sql2 = "UPDATE accounts SET apiHash = ? WHERE id = ?";
                $query = $controller->db->query($sql2, array($newHash, $row->id));
            } else {
                $row->hash = $newHash;
                $sql2 = "UPDATE accounts SET hash = ? WHERE id = ?";
                $query = $controller->db->query($sql2, array($newHash, $row->id));
            }
            set_cookie(array(
                'name'   => 'session',
                'value'  => $row->hash,
                'expire' => 60*60*60,
            ));
            return $row;
        } else {
            return null;
        }
    }

    public static function logout($controller, $id)
    {
        $newHash = static::generateHash();
        $sql2 = "UPDATE accounts SET hash = ? WHERE id = ?";
        $query = $controller->db->query($sql2, array($newHash, $id));
        delete_cookie('session');
    }

    public static function check($controller, $hash, $checkApiHash=false)
    {
        if($checkApiHash) {
            $sql = "SELECT id,username,hash,role FROM accounts WHERE apiHash = ?";
        } else {
            $sql = "SELECT id,username,hash,role FROM accounts WHERE hash = ?";
        }
        $query = $controller->db->query($sql, array($hash));
        $row = $query->row();
        return $row;
    }

    public static function foundAccountByPassword($controller, $password)
    {
        $sql = "SELECT id,username,hash,role FROM accounts WHERE password = ?";
        $query = $controller->db->query($sql, array(sha1($password)));
        $row = $query->row();
        return is_object($row);
    }
}