<?php
require_once '../config.php';
class Login extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;

		parent::__construct();
		ini_set('display_error', 1);
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function index(){
		echo "<h1>Access Denied</h1> <a href='".base_url."'>Go Back.</a>";
	}
	public function login(){
		extract($_POST);
		$stmt = $this->conn->prepare("SELECT * from users where username = ? and password = ? ");
		$pw = $password;
		$stmt->bind_param('ss',$username,$pw);
		$stmt->execute();
		$qry = $stmt->get_result();
		if($qry->num_rows > 0){
			$res = $qry->fetch_array();
			if($res['status'] != 1){
				return json_encode(array('status'=>'notverified'));
			}
			foreach($res as $k => $v){
				if(!is_numeric($k) && $k != 'password'){
					$this->settings->set_userdata($k,$v);
				}
			}
			$this->settings->set_userdata('login_type',1);
		return json_encode(array('status'=>'success'));
		}else{
		return json_encode(array('status'=>'incorrect','error'=>$this->conn->error));
		}
	}
	public function logout(){
		if($this->settings->sess_des()){
			redirect('admin/login.php');
		}
	}
	function client_login(){
		extract($_POST);
        
        // 1. Try Admin Login (User Table) - mapping 'email' input to 'username'
        // Using strict logic from login() function
		$stmt_admin = $this->conn->prepare("SELECT * from users where username = ? and password = ? ");
		$stmt_admin->bind_param('ss',$email, $password); // $email is the input name
		$stmt_admin->execute();
		$qry_admin = $stmt_admin->get_result();
        
        if($qry_admin->num_rows > 0){
			$res = $qry_admin->fetch_array();
            // Admin Found
			if($res['status'] != 1){
				// return json_encode(array('status'=>'notverified')); // Admin usually doesn't have this, but keeping safe
			}
			foreach($res as $k => $v){
				if(!is_numeric($k) && $k != 'password'){
					$this->settings->set_userdata($k,$v);
				}
			}
			$this->settings->set_userdata('login_type',1); // 1 = Admin
		    return json_encode(array('status'=>'success', 'type'=>1));
        }

        // 2. Try Client Login (Client List Table)
		$stmt = $this->conn->prepare("SELECT *,concat(lastname,', ',firstname,' ',middlename) as fullname from client_list where email = ?");
		$stmt->bind_param('s',$email);
		$stmt->execute();
		$qry = $stmt->get_result();
		if($this->conn->error){
			$resp['status'] = 'failed';
			$resp['msg'] = "An error occurred while fetching data. Error:". $this->conn->error;
		}else{
			if($qry->num_rows > 0){
				$res = $qry->fetch_array();
				if(password_verify($password, $res['password'])){
					// Valid Hash
					foreach($res as $k => $v){
						$this->settings->set_userdata($k,$v);
					}
					$this->settings->set_userdata('login_type',2);
					$resp['status'] = 'success';
                    $resp['type'] = 2; // 2 = Client
				} elseif ($password === $res['password']) {
					// Fallback: Plain Text Match
					$new_hash = password_hash($password, PASSWORD_DEFAULT);
					$uid = $res['id'];
					$this->conn->query("UPDATE client_list SET `password` = '{$new_hash}' WHERE id = {$uid}");
					foreach($res as $k => $v){
						$this->settings->set_userdata($k,$v);
					}
					$this->settings->set_userdata('login_type',2);
					$resp['status'] = 'success';
                    $resp['type'] = 2;
				} else {
					$resp['status'] = 'incorrect'; // Specific status for bad password
					$resp['msg'] = "Invalid email or password.";
				}
			}else{
				$resp['status'] = 'incorrect';
				$resp['msg'] = "Invalid email or password.";
			}
		}
		return json_encode($resp);
	}
	public function client_logout(){
		if($this->settings->sess_des()){
			redirect('./login.php');
		}
	}
}
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new Login();
switch ($action) {
	case 'login':
		echo $auth->login();
		break;
	case 'logout':
		echo $auth->logout();
		break;
	case 'clogin':
		echo $auth->client_login();
		break;
	case 'clogout':
		echo $auth->client_logout();
		break;
	default:
		echo $auth->index();
		break;
}

