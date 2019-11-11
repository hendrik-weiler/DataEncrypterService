<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Api extends CI_Controller
{
    public function login()
    {
        $username = $this->input->get('username');
        $password = $this->input->get('password');

        $result = $this->authentication->login($this, $username, $password, true);
        if(is_object($result)) {
            print $result->apiHash;
        }
    }

    public function checkSession()
    {
        $session = $this->input->get('session');
        $result = $this->authentication->check($this, $session, true);
        if(is_object($result)) {
            print 1;
        }
    }
}