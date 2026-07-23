<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

    /**
     * DOCU: Default controller - displays welcome message <br>
     * Triggered by: GET / (default route) <br>
     * Last Updated Date: July 14, 2026
     * @return void
     * @author Sam
     */
    public function index()
    {
        $this->load->view('welcome_message');
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
