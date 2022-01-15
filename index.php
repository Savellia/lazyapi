<?php
  $_SESSION['access'] = false;
  require "config.php";
  session_destroy();
  session_start();
  require "core/engine.php";

  if(file_exists("./install/")) {
    // Step 1 not complete.
    if(!$_ENV['dbAccess'] instanceof PDO){
      header("Location: ./install");
    }
    // Step 2 not complete.
    if(!installSQLChecker()){
      header("Location: ./install");
    }
    // Step 3 not complete.
    if(!userAPIAccessChecker()){
      header("Location: ./install");
    }
  }

  if(isset($_POST["user"]) && !empty($_POST["user"]) && isset($_POST["password"]) && !empty($_POST["password"])){
    $user = htmlentities(addslashes($_POST["user"]));
    $password = htmlentities(addslashes($_POST["password"]));

    if($_ENV["apiConnector"]["apiUser"] == $user && $_ENV["apiConnector"]["apiPass"] == $password){
      $_SESSION['access'] = true;
      header("Location: panel");
    }
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <title>Lazyapi - Login</title>
  </head>
  <body>
    <div class="container">
      <div class="my-2">
        <div class="row">
          <h1 class="display-5 p-0">Login</h1>
        </div>
        <form action="" method="post">
          <div class="row">
            <div class="col-5 endpoint  rounded my-2 p-3 border">
              <div class="row mb-3 justify-content-center">
                <div class="col-6">
                  <label for="user" class="form-label">User</label>
                  <input type="text" class="form-control" id="user" name="user" required>
                </div>
              </div>
              <div class="row mb-3 justify-content-center">
                <div class="col-6">
                  <label for="password" class="form-label">Password</label>
                  <input type="password" class="form-control" id="password" name="password" required>
                </div>
              </div>
                <div class="row justify-content-center">
                  <div class="col-auto">
                    <?php
                      if (!file_exists("./install/")) {
                        echo "<button type=\"submit\" class=\"goto rounded px-2 py-1\"><i class=\"bi bi-box-arrow-in-right\"></i> Sign in</button>";
                      }else{
                        echo "<button class=\"delete  rounded px-2 py-1\">Please, delete the folder './install' before to sign in.</button> <button class=\"normal rounded px-2 py-1\"  data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"Reload\" onClick=\"window.location.reload();\"><i class=\"bi bi-arrow-clockwise\"></i></button>";
                      }
                    ?>
                  </div>
                </div>
            </div>
          </div>
        </form>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script type="text/javascript">
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
      })
    </script>
  </body>
</html>