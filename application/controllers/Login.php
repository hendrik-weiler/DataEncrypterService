<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    function __construct()
    {
        parent::__construct();

        $user = $this->authentication->check($this, get_cookie('session'));
        if(is_object($user)) {
            redirect('/admin/files');
        }

        $this->layout->data['title'] = 'Login';
    }

	public function index()
	{
		$this->layout->render();
	}

    public function tryout()
    {
        $user = $this->authentication->login($this, $this->input->post('username'), $this->input->post('password'));
        if(is_object($user)) {
            $this->layout->data['success'] = true;
        } else {
            $this->layout->data['success'] = false;
        }
        $this->layout->render();
	}
}
