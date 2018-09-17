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
          $data['email_err'] = 'Entrez une adresse mail SVP';
        } else {
          if($this->userModel->findUserbyEmail($data['email'])) {
            $data['email_err'] = 'Email déjà pris';
          }
        }

        if(empty($data['name'])) {
          $data['name_err'] = 'Entrez un nom SVP';
        }

        if(empty($data['password'])) {
          $data['password_err'] = 'Entrez un mot de passe SVP';
        } elseif(strlen($data['password']) < 6) {
            $data['password_err'] = 'Entrez un mot de passe avec au moins 6 caractères';
        }

        if(empty($data['confirm_password'])) {
          $data['confirm_password_err'] = 'Confirmez votre mot de passe SVP';
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
            flash('register_success', 'Vous êtes enregistré(e) et pouvez vous connecter');
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
          $data['email_err'] = 'Entrez votre mail SVP';
        }

        //check user/email
        if($this->userModel->findUserByEmail($data['email'])) {
          //user found
        } else {
          //user not found
          $data['email_err'] = 'Utilisateur no trouvé';
        }

        if(empty($data['password'])) {
          $data['password_err'] = 'Entrez votre mot de passe SVP';
        } elseif(strlen($data['password']) < 6) {
            $data['password_err'] = 'Entrez un mot de passe avec au moins 6 caractères';
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