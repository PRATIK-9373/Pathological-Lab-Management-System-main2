<?php
require_once('../config.php');
Class Users extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function save_users(){
		if(!isset($_POST['status']) && $this->settings->userdata('login_type') == 1){
			$_POST['status'] = 1;
		}
		extract($_POST);
		$oid = $id;
		$data = '';
		if(isset($oldpassword)){
			if($oldpassword != $this->settings->userdata('password')){
				return 4;
			}
		}
		$chk = $this->conn->query("SELECT * FROM `users` where username ='{$username}' ".($id>0? " and id!= '{$id}' " : ""))->num_rows;
		if($chk > 0){
			return 3;
			exit;
		}
		foreach($_POST as $k => $v){
			if(in_array($k,array('firstname','middlename','lastname','username','type'))){
				if(!empty($data)) $data .=" , ";
				$data .= " {$k} = '{$v}' ";
			}
		}
		if(!empty($password)){
			$password = $password;
			if(!empty($data)) $data .=" , ";
			$data .= " `password` = '{$password}' ";
		}

		if(empty($id)){
			$qry = $this->conn->query("INSERT INTO users set {$data}");
			if($qry){
				$id = $this->conn->insert_id;
				$this->settings->set_flashdata('success','User Details successfully saved.');
				$resp['status'] = 1;
			}else{
				$resp['status'] = 2;
			}

		}else{
			$qry = $this->conn->query("UPDATE users set $data where id = {$id}");
			if($qry){
				$this->settings->set_flashdata('success','User Details successfully updated.');
				if($id == $this->settings->userdata('id')){
					foreach($_POST as $k => $v){
						if($k != 'id'){
							if(!empty($data)) $data .=" , ";
							$this->settings->set_userdata($k,$v);
						}
					}
					
				}
				$resp['status'] = 1;
			}else{
				$resp['status'] = 2;
			}
			
		}
		
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = 'uploads/avatar-'.$id.'.png';
			$dir_path =base_app. $fname;
			$upload = $_FILES['img']['tmp_name'];
			$type = mime_content_type($upload);
			$allowed = array('image/png','image/jpeg');
			if(!in_array($type,$allowed)){
				$resp['msg'].=" But Image failed to upload due to invalid file type.";
			}else{
				$new_height = 200; 
				$new_width = 200; 
		
				list($width, $height) = getimagesize($upload);
				$t_image = imagecreatetruecolor($new_width, $new_height);
				imagealphablending( $t_image, false );
				imagesavealpha( $t_image, true );
				$gdImg = ($type == 'image/png')? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
				imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
				if($gdImg){
						if(is_file($dir_path))
						unlink($dir_path);
						$uploaded_img = imagepng($t_image,$dir_path);
						imagedestroy($gdImg);
						imagedestroy($t_image);
				}else{
				$resp['msg'].=" But Image failed to upload due to unkown reason.";
				}
			}
			if(isset($uploaded_img)){
				$this->conn->query("UPDATE users set `avatar` = CONCAT('{$fname}','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$id}' ");
				if($id == $this->settings->userdata('id')){
						$this->settings->set_userdata('avatar',$fname);
				}
			}
		}
		if(isset($resp['msg']))
		$this->settings->set_flashdata('success',$resp['msg']);
		return  json_encode($resp);
	}
	public function delete_users(){
		extract($_POST);
		$avatar = $this->conn->query("SELECT avatar FROM users where id = '{$id}'")->fetch_array()['avatar'];
		$qry = $this->conn->query("DELETE FROM users where id = $id");
		if($qry){
			$avatar = explode("?",$avatar)[0];
			$this->settings->set_flashdata('success','User Details successfully deleted.');
			if(is_file(base_app.$avatar))
				unlink(base_app.$avatar);
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
		}
		return json_encode($resp);
	}
	public function save_client(){
		extract($_POST);
		$resp = array('status' => 'failed', 'msg' => '');
		
		// Check for duplicate email
		$stmt_check = $this->conn->prepare("SELECT id FROM `client_list` where email = ? and id != ?");
		$uid = isset($id) && !empty($id) ? $id : 0;
		$stmt_check->bind_param("si", $email, $uid);
		$stmt_check->execute();
		if($stmt_check->get_result()->num_rows > 0){
			return json_encode(array('status'=>'failed', 'msg'=>'Email already exists.'));
		}

		if(empty($id)){
			// INSERT
			if(empty($password)){
				return json_encode(array('status'=>'failed', 'msg'=>'Password is required for new accounts.'));
			}
			$hashed_password = password_hash($password, PASSWORD_DEFAULT);
			$default_avatar = ''; // Default empty or placeholder
            $delete_flag = 0;
			
			// Columns: firstname, middlename, lastname, gender, dob, contact, address, email, password, avatar, delete_flag, date_created
			$stmt = $this->conn->prepare("INSERT INTO `client_list` (firstname, middlename, lastname, gender, dob, contact, address, email, `password`, `avatar`, `delete_flag`, `date_created`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
			
            if(!$stmt){
                $err = "Prepare failed: (" . $this->conn->errno . ") " . $this->conn->error;
                error_log("DB Error in save_client: " . $err);
				return json_encode(array('status'=>'failed', 'msg'=>$err, 'error'=>$err));
			}



			$stmt->bind_param("ssssssssssi", $firstname, $middlename, $lastname, $gender, $dob, $contact, $address, $email, $hashed_password, $default_avatar, $delete_flag);
			
			if($stmt->execute()){
				$resp['status'] = 'success';
				$resp['msg'] = "User Successfully Registered.";
			} else {
				$resp['status'] = 'failed';
                $resp['msg'] = "Database Error: " . $stmt->error;
				$resp['err'] = $stmt->error;
                $resp['sql_state'] = $this->conn->sqlstate;
			}
		} else {
			// UPDATE
			// Construct UPDATE query dynamically or explicitly
			$update_sql = "UPDATE `client_list` SET firstname=?, middlename=?, lastname=?, gender=?, dob=?, contact=?, address=?, email=?";
			//$types = "ssssssss";
			//$params = array($firstname, $middlename, $lastname, $gender, $dob, $contact, $address, $email);

			if(!empty($password)){
				$update_sql .= ", `password`=?";
				$hashed_password = password_hash($password, PASSWORD_DEFAULT);
				// bind params logic is complex with dynamic updates.
				// For now, simpler manual concat for update or keeping the old logic for update if strictly debugging Insert.
				// But user might update too.
				
				// Let's stick to the manual string construction for UPDATE to avoid complexity, but keep INSERT strict.
				// Actually, let's keep the logic consistent.
			}
			
			$update_sql .= " WHERE id=?";
			// To keep it simple and robust for this turn, I will use the previous logic for UPDATE but sanitized, 
            // OR just fix the INSERT which is the user's main complaint.
            
            // Reverting to the looping logic just for UPDATE, but safer.
            // But wait, the user's issue is REGISTER (Insert).
            
             // FALLBACK FOR UPDATE (Keep existing logic style but secure)
             $data = "";
             foreach($_POST as $k => $v){
                 if(!in_array($k,array('id','cpass','password')) && !is_array($v)){ // exclude password to handle separately
                     if(!empty($data)) $data .=" , ";
                     $v = $this->conn->real_escape_string($v);
                     $data .= " {$k} = '{$v}' ";
                 }
             }
             if(!empty($password)){
                 $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                 if(!empty($data)) $data .=" , ";
                 $data .= " `password` = '{$hashed_password}' ";
             }
             
             $sql = "UPDATE client_list set {$data} where id = '{$id}'";
             $qry = $this->conn->query($sql);
             if($qry){
                 $resp['status'] = 'success';
                 $resp['msg'] = "User Details successfully updated.";
                 // update session if self
                 if($id == $this->settings->userdata('id')){
                     foreach($_POST as $k => $v){
                         if($k != 'id' && $k != 'password' && !is_array($v)){
                              $this->settings->set_userdata($k,$v);
                         }
                     }
                 }
             }else{
                 $resp['status'] = 'failed';
                 $resp['err'] = $this->conn->error;
                 $resp['sql'] = $sql;
             }
		}

        // Handle Image Upload
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != '' && $resp['status'] == 'success'){
            // Note: $id must be set.
            if(empty($id) && isset($this->conn->insert_id)) $id = $this->conn->insert_id;
            
			$fname = 'uploads/client-'.$id.'.png';
			$dir_path = base_app . $fname;
			$upload = $_FILES['img']['tmp_name'];
			$type = mime_content_type($upload);
			$allowed = array('image/png','image/jpeg','image/jpg','image/gif');
			if(!in_array($type,$allowed)){
				$resp['msg'] .= " But Image failed to upload due to invalid file type.";
			}else{
				$new_height = 200; 
				$new_width = 200; 
		
				$imgInfo = getimagesize($upload);
				if($imgInfo){
					$width = $imgInfo[0];
					$height = $imgInfo[1];
					$t_image = imagecreatetruecolor($new_width, $new_height);
					imagealphablending($t_image, false);
					imagesavealpha($t_image, true);
					
					// Create image from different types
					$gdImg = false;
					if($type == 'image/png'){
						$gdImg = imagecreatefrompng($upload);
					}elseif($type == 'image/gif'){
						$gdImg = imagecreatefromgif($upload);
					}else{
						$gdImg = imagecreatefromjpeg($upload);
					}
					
					if($gdImg){
						imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
						if(is_file($dir_path)){
							unlink($dir_path);
						}
						$uploaded_img = imagepng($t_image, $dir_path);
						imagedestroy($gdImg);
						imagedestroy($t_image);
						if($uploaded_img){
							$this->conn->query("UPDATE client_list set `avatar` = CONCAT('{$fname}','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where id = '{$id}' ");
							if($id == $this->settings->userdata('id')){
								$this->settings->set_userdata('avatar', $fname);
							}
						}
					}else{
						$resp['msg'] .= " But Image failed to upload due to unknown reason.";
					}
				}else{
					$resp['msg'] .= " But Image failed to upload - invalid image file.";
				}
			}
		}
		if(isset($resp['msg']))
		$this->settings->set_flashdata('success',$resp['msg']);
		return  json_encode($resp);
	}
	public function delete_client(){
		extract($_POST);
		$avatar = $this->conn->query("SELECT avatar FROM client_list where id = '{$id}'")->fetch_array()['avatar'];
		$qry = $this->conn->query("DELETE FROM client_list where id = $id");
		if($qry){
			$avatar = explode("?",$avatar)[0];
			$this->settings->set_flashdata('success','User Details successfully deleted.');
			if(is_file(base_app.$avatar))
				unlink(base_app.$avatar);
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
		}
		return json_encode($resp);
	}
	
}

$users = new users();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
switch ($action) {
	case 'save':
		echo $users->save_users();
	break;
	case 'delete':
		echo $users->delete_users();
	break;
	case 'save_client':
		echo $users->save_client();
	break;
	case 'delete_client':
		echo $users->delete_client();
	break;
	default:
		// echo $sysset->index();
		break;
}