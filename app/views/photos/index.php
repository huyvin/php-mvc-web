<?php require APPROOT.'/views/inc/header.php'; ?>
<!--<?php print_r($_SESSION); ?> -->
<main role="main">

  <section class="jumbotron text-center">
    <div class="container">
      <h1 class="jumbotron-heading">Bienvenue sur votre album <?php echo $_SESSION['user_name'] ?> !</h1>
      <p class="lead text-muted">Vous pouvez ici ajouter des images et consulter votre galerie d'images</p>
      <p>
        <form method="POST" enctype="multipart/form-data" action="<?php echo URLROOT; ?>/photos/add">
            <input type="hidden" name="MAX_FILE_SIZE" value="1000000">
            <input type="file" name="fichier" size="30" >
            <input type="submit" name="upload" value="Envoyer l'image" class="btn btn-primary my-2">
        </form>
      </p>
    </div>
  </section>

  <div class="album py-5 bg-light">
    <div class="container">
      <div class="row">
      <?php 
        $dir = '../public/img/'.$_SESSION['user_email'];
        $files = preg_grep('/^([^.])/', scandir($dir));

        //print_r($files);
        
        foreach($files as $file) { ?>
          <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
              <img class="card-img-top" src="<?php echo 'img/'.$_SESSION['user_email'].'/'.$file ?>"" alt="">
              <div class="card-body">
                
                <div class="d-flex justify-content-between align-items-center">
                  <div class="btn-group">
                    <form method="post" action="<?php echo URLROOT; ?>/photos/delete">
                      <button type="submit" class="btn btn-sm btn-danger" name="filename" value="<?php echo $file ?>">Suppr.</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>
    </div>
  </div>
</main>

<?php require APPROOT.'/views/inc/footer.php'; ?>