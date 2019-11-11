<?php


class Admin extends CI_Controller
{
    protected $user;

    function __construct()
    {
        parent::__construct();

        $user = $this->authentication->check($this, get_cookie('session'));
        if(is_object($user)) {
            $this->user = $user;
        } else {
            redirect('/login');
        }

        $this->layout->layout = 'admin';
        $this->layout->data['user'] = $user;
        $this->layout->data['mode'] = 0;
    }

    public function files() {
        $this->layout->data['title'] = 'Admin - Files';
        $this->layout->data['mode'] = 1;
        $this->layout->render();
    }

    public function profile()
    {
        $this->layout->data['title'] = 'Admin - Profile';
        $this->layout->data['mode'] = 3;
        $this->layout->render();
    }

    public function logout()
    {
        $this->authentication->logout($this, $this->user->id);
        redirect('/login');
    }

    public function profile_save()
    {
        $this->layout->data['title'] = 'Admin - Profile';

        if ($this->authentication->foundAccountByPassword($this, $this->input->post('password_old'))) {
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[8]');
            $this->form_validation->set_rules('password_repeat', 'Password Confirmation', 'trim|required|matches[password]');

            if ($this->form_validation->run() == FALSE)
            {
                $this->layout->data['mode'] = 3;
                $this->layout->view = 'profile';
                $this->layout->render();
            } else {

                $sql = "UPDATE accounts SET password = ? WHERE id = ?";
                $query = $this->db->query($sql, array(sha1($this->input->post('password')), $this->user->id));
                $this->layout->data['mode'] = 3;
                $this->layout->view = 'profile';
                $this->session->set_flashdata('success', 'success');
                $this->layout->render();

            }
        } else {
            $this->session->set_flashdata('error', 'foundAccountByPassword');
            redirect('/admin/profile');
        }

    }
}