<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Install extends CI_Controller
{
    private function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir."/".$object) && !is_link($dir."/".$object))
                        rrmdir($dir."/".$object);
                    else
                        unlink($dir."/".$object);
                }
            }
            rmdir($dir);
        }
    }

    public function __construct()
    {
        parent::__construct();

        if(file_exists('disable_installation')) {
            print 'Installation is disabled';
            exit;
        }
    }

    public function index()
    {
        $this->layout->data = array(
            'title' => 'Installation',
            'sqliteEnabled' => extension_loaded('sqlite3'),
            'folderIsWriteable' => is_writable('.'),
            'folderPath' => realpath(dirname(__FILE__).'/..')
        );
        $this->layout->render();
    }

    public function execute()
    {
        // if its not writeable do not permit execution
        if(!is_writable('.')) exit;

        $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[5]|max_length[12]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[8]');
        $this->form_validation->set_rules('password_repeat', 'Password Confirmation', 'trim|required|matches[password]');

        if ($this->form_validation->run() == FALSE)
        {
            $this->layout->data = array(
                'title' => 'Installation',
                'sqliteEnabled' => extension_loaded('sqlite3')
            );
            $this->layout->view = 'index';
            $this->layout->render();
        }
        else
        {
            if(is_dir('db')) {
                $this->rrmdir('db');
            }
            mkdir('db');
            file_put_contents('db/.htaccess', 'Order Allow,Deny'.PHP_EOL.'Deny from All');


            $dbname = 'db/'.uniqid(rand(),true) . '.db';

            if (extension_loaded('sqlite3')) {

                $dbBackup = file_get_contents(APPPATH . 'config/database.php.bak');
                file_put_contents(APPPATH . 'config/database.php', str_replace('[database]', (str_replace('//','/',APPPATH . '/../') . $dbname), $dbBackup));

                try {
                    $db = new SQLite3($dbname);
                    $db-> exec("
CREATE TABLE `accounts` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `hash` varchar(100) NOT NULL,
  `apiHash` varchar(100) NOT NULL,
  `role` int(11) NOT NULL
);
");
                    $stmt = $db->prepare("INSERT INTO `accounts` (`id`, `username`, `password`, `hash`, `apiHash`,`role`) VALUES (NULL, :username, :password, :hash, :apiHash,'1');");
                    $stmt->bindValue(':username', $this->input->post('username'), SQLITE3_TEXT);
                    $stmt->bindValue(':password', sha1($this->input->post('password')), SQLITE3_TEXT);
                    $stmt->bindValue(':hash', $this->encryption->create_key(32), SQLITE3_TEXT);
                    $stmt->bindValue(':apiHash', $this->encryption->create_key(32), SQLITE3_TEXT);
                    $stmt->execute();

                    file_put_contents('disable_installation','disable_installation');

                    $this->layout->view = 'success';
                    $this->layout->render();
                } catch (Exception $exception) {
                    echo $exception->getMessage();
                }
            }
        }


    }

}