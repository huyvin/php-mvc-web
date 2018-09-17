<?php
  class Users extends Controller {
    public function __construct() {
      $this->userModel = $this->model('User');
    }

    public function index() {

    }

    public function register() {
      // check for post
      if($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);


        $data = [
          'name' => trim($_POST['name']),
          'email' => trim($_POST['email']),
          'password' => trim($_POST['password']),
          'confirm_password' => trim($_POST['confirm_password']),
          'name_err' => '',
          'email_err' => '',
          'password_err' => '',
          'confirm_password_err' => ''
        ];

        if(empty($data['email'])) {
          $data['email_err'] = 'please enter email';
        } else {
          if($this->userModel->findUserbyEmail($data['email'])) {
            $data['email_err'] = 'email already taken';
          }
        }

        if(empty($data['name'])) {
          $data['name_err'] = 'please enter name';
        }

        if(empty($data['password'])) {
          $data['password_err'] = 'please enter password';
        } elseif(strlen($data['password']) < 6) {
            $data['password_err'] = 'password at least 6 chars';
        }

        if(empty($data['confirm_password'])) {
          $data['confirm_password_err'] = 'please enter confirm password';
        } else {
          if($data['password'] != $data['confirm_password']) {
            $data['confirm_password_err'] = 'passwords do not match';
          }
        }

        //check errors are empty
        if(empty($data['email_err']) && empty($data['name_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
          //hash password
          $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);


          //register user
          if($this->userModel->register($data)) {
            flash('register_success', 'You are registered and can log in');
            redirect('users/login'); //fonction redirect dans url_helper.php
          } else {
            die('something went wrong');
          }
        } else {
          $this->view('users/register', $data);
        }
        
      } else {
        //init data
        $data = [
          'name' => '',
          'email' => '',
          'password' => '',
          'confirm_password' => '',
          'name_err' => '',
          'email_err' => '',
          'password_err' => '',
          'confirm_password_err' => ''
        ];

        //load view
        $this->view('users/register', $data);

      }
    }

    public function login() {
      // check for post
      if($_SERVER['REQUEST_METHOD'] == 'POST') {

        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $data = [
          'email' => trim($_POST['email']),
          'password' => trim($_POST['password']),
          'email_err' => '',
          'password_err' => '',
          
        ];

        if(empty($data['email'])) {
          $data['email_err'] = 'please enter email';
        }

        //check user/email
        if($this->userModel->findUserByEmail($data['email'])) {
          //user found
        } else {
          //user not found
          $data['email_err'] = 'no user found';
        }

        if(empty($data['password'])) {
          $data['password_err'] = 'please enter password';
        } elseif(strlen($data['password']) < 6) {
            $data['password_err'] = 'password at least 6 chars';
        }

        //check errors are empty
        if(empty($data['email_err']) && empty($data['password_err'])) {
          $loggedInUser = $this->userModel->login($data['email'], $data['password']);

          if($loggedInUser) {
            $this->createUserSession($loggedInUser);
            
          } else {
            $data['password_err'] = 'Password incorrect';

            $this->view('users/login', $data);
          }
        } else {
          $this->view('users/login', $data);
        }
      } else {
        //init data
        $data = [
          'email' => '',
          'password' => '',
          'email_err' => '',
          'password_err' => '',
          
        ];

        //load view
        $this->view('users/login', $data);

      }
    }

    public function createUserSession($user) {
      $_SESSION['user_id'] = $user->id;
      $_SESSION['user_email'] = $user->email;
      $_SESSION['user_name'] = $user->name;
      redirect('photos');
    }

    public function logout() {
      unset($_SESSION['user_id']);
      unset($_SESSION['user_email']);
      unset($_SESSION['user_name']);
      session_destroy();
      redirect('users/login');
    }

    

  }