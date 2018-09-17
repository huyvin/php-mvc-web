<?php
  class Photos extends Controller {

    public function __construct() {
      if(!isLoggedIn()) {
        redirect('users/login');
      }
    }

    public function index() {

      $data = [];

      $this->view('photos/index', $data);
    }

    public function test() {
      $data = [];

      $this->view('photos/test', $data);
    }

    public function add() {
      $dirname = $_SESSION['user_email'];

      if(!is_dir('../public/img/'.$dirname)) {
        mkdir('../public/img/'.$dirname);
      }

      if( isset($_POST['upload']) ) { // si formulaire soumis
        $content_dir = '../public/img/'.$dirname; // dossier où sera déplacé le fichier

        $tmp_file = $_FILES['fichier']['tmp_name'];

        if( !is_uploaded_file($tmp_file) )
        {
            exit("Le fichier est introuvable");
        }

        // on vérifie maintenant l'extension
        $type_file = $_FILES['fichier']['type'];

        if( !strstr($type_file, 'jpg') && !strstr($type_file, 'jpeg') && !strstr($type_file, 'bmp') && !strstr($type_file, 'gif') )
        {
            exit("Le fichier n'est pas une image");
        }

        // on copie le fichier dans le dossier de destination
        $name_file = $_FILES['fichier']['name'];


        $temp = explode(".", $_FILES["fichier"]["name"]);
        $newfilename = round(microtime(true)) . '.' . end($temp);


        if( !move_uploaded_file($tmp_file, $content_dir .'/'. $newfilename) )
        {
            exit("Impossible de copier le fichier dans $content_dir");
        }

        redirect('photos');
      }
    }


    public function delete() {
      $filename = $_POST['filename'];
      //echo print_r($_SESSION);

      $dirname = $_SESSION['user_email'];

      unlink('../public/img/'.$dirname.'/'.$filename);

      redirect('photos');
    }
  }