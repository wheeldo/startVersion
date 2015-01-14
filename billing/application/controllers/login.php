<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	function __construct()
        {
            // Call the Model constructor
            parent::__construct();
            $this->load->helper('url');	
            $this->load->library('session');
        }
        
        
	public function index()
	{
            $session_data=$this->session->all_userdata();
            
            ////
//            $array_items = array('login_user_id' => '', 'login_user_email' => '', 'login_user_name' => '');
//            $this->session->unset_userdata($array_items);
            ////
            
            if(!isset($session_data['login_user_id'])) {
                $this->login();
                return;
            }
            
            redirect('/bluesnap/index');
	}
        
        public function login() {
            $this->load->view('login_view');
        }
        
        public function loginCheck() {
            $res=array();
            $post = $this->input->post();
            $email = isset($post['email'])?$post['email']:false;
            $password = isset($post['password'])?$post['password']:false;
            
            if(!$email || !$password) {
                $res['status']="faild";
                $res['error']="missing data";
            }
            else {
                $this->load->model('login_model');
                $user_data=$this->login_model->login($email,$password);
                if(!$user_data) {
                    $res['status']="faild";
                    $res['error']="login faild";
                }
                else {
                    $user_session = array(
                        'login_user_id'  => $user_data->userID,
                        'login_user_email'     => $user_data->userEmail,
                        'login_user_name' => $user_data->userName
                    );
                    
                    $this->session->set_userdata($user_session);
                    
                    $res['status']="ok";
                }
            }
            
            header('Content-Type:application/json');
            echo json_encode($res);
            
            //var_dump($email);
        }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */