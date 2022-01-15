<?php
  require "../config.php";
  require "../core/engine.php";
?>

<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="../css/style.css" />
    <title>LazyAPI - Step 3</title>
  </head>
  <body>
    <div class="container">
      <div class="my-2">
        <div class="row">
          <h1 class="display-5 p-0">Welcome to LazyAPI</h1>
        </div>
        <div class="row">
          <a href="index2" class="p-0"><button class="back rounded px-2 py-1 col-auto"><i class="bi bi-arrow-left"></i> Back</button></a>
        </div>

        <div class="endpoint row rounded my-2 p-3 border">
          <div class="col-6">
            <div class="row">
              <h3 class="col-auto">STEP 3</h3>
            </div>
            <div class="row">
              <h4 class="col-auto fw-light">User API access</h4>
            </div>
            <div class="mb-3">
              <ol>
                <li>Open the file 'config.php'.</li>
                <li>Change information by yours in "apiConnector" section.</li>
                <li>Save.</li>
                <li>The icon below (<i class='bi bi-person'></i>) should turn to green after a refresh (<i class='bi bi-arrow-clockwise'></i>).</li>
              </ol>
              <?php
                if(userAPIAccessChecker()){
                  echo "<button class='goto rounded px-2 py-1' data-bs-toggle='tooltip' data-bs-placement='top' title='API user access ready.'><i class='bi bi-person'></i></button>";
                }else{
                  echo "<button class='delete rounded px-2 py-1' data-bs-toggle='tooltip' data-bs-placement='top' title='API user access not written.'><i class='bi bi-person'></i></button>";
                }
              ?>
              <button class='normal rounded px-2 py-1'  data-bs-toggle='tooltip' data-bs-placement='top' title='Reload' onClick='window.location.reload();'><i class='bi bi-arrow-clockwise'></i></button>
            </div>
          </div>

          <?php
            if(userAPIAccessChecker()){
              echo "<div class=\"col-12\"><div class=\"row\"><div class=\"col-auto\"><a href=\"../\"><button class=\"goto rounded px-2 py-1\"><i class=\"bi bi-check2\"></i> Installation done</button></a></div></div></div>";
            }
          ?>
        </div>
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