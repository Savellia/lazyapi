<?php
  require "db.php";
  require "core/engine.php";

  $delete = null;

  if(isset($_POST["idEndpointToBeDeleted"]) && is_numeric($_POST["idEndpointToBeDeleted"])){
    $idEndpointToBeDeleted = htmlentities(addslashes($_POST["idEndpointToBeDeleted"]));
    $delete = deleteEndpoint($idEndpointToBeDeleted);
  }
  $access = true;
?>

<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <title>Endpoints list</title>
  </head>
  <body>
    <div class="container">
      <div class="endpoints-list my-2">

        <div class="row">
          <h1 class="display-5 p-0">LazyAPI - 1.0</h1>
        </div>

        <div class="row">
          <?php
            if($delete){
              echo "<button class='goto rounded px-2 py-1 mb-2'>The endpoint has been deleted 😀</button>";
            }elseif($delete === false){
              echo "<button class='delete rounded px-2 py-1 mb-2'>An error has been occured 😭</button>";
            }
          ?>
        </div>

        <div class="row">
          <a href="create" class="p-0 col-auto"><button class="add rounded px-2 py-1 col-auto"><i class="bi bi-plus-circle"></i> New endpoint</button></a>
          <a href="faq" class="p-0 ms-2 col-auto"><button class="normal rounded px-2 py-1 col-auto me-auto"><i class="bi bi-question-circle"></i> FAQ</button></a>
          <?php
            if($_ENV['dbAccess'] instanceof PDO){
              echo "<button class='goto ms-2 rounded px-2 py-1 col-auto' data-bs-toggle='tooltip' data-bs-placement='top' title='Connected to the database'><i class='bi bi-server'></i></button>";
            }else{
              echo "<a href='faq' class='p-0 ms-2 col-auto'><button class='delete rounded px-2 py-1 col-auto' data-bs-toggle='tooltip' data-bs-placement='top' title='".$_ENV['dbAccess']."'><i class='bi bi-server'></i></button></a>";
              $access = false;
            }
          ?>
        </div>

        <?php
          if($access){
            foreach (getEndpoints() as $k => $v) {
              echo "<div class=\"endpoint row rounded my-2 p-3 border ".(!$v["available"] ? "off" : "")."\">";
                echo "<div class=\"col\">";
                  echo "<div class=\"d-block\">";
                    echo "<span class=\"method rounded p-1 ".($v["method"] == "GET" ? "get" : "post")."\">".$v["method"]."</span> <span class=\"name\">".$v["name"]."</span>";
                  echo "</div>";
                  echo "<span class=\"description d-block\">".$v["description"]."</span>";
                echo "</div>";
                echo "<div class=\"col-auto actions align-self-center\">";
                  echo ($v["available"] ? " <a href=\"v1?endpoint=".$v["name"]."\"><button class=\"goto rounded px-2 py-1\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"Go to endpoint\"><i class=\"bi bi-arrow-right\"></i></button></a>" : "");
                  echo " <a href='edit?id=".$v["id"]."'><button class=\"edit rounded px-2 py-1\" data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"Edit the endpoint\"><i class=\"bi bi-pencil\"></i></button></a>";
                  echo " <span data-bs-toggle=\"tooltip\" data-bs-placement=\"top\" title=\"Delete the endpoint\"><button class=\"delete rounded px-2 py-1\" data-bs-toggle=\"modal\" data-bs-target=\"#modalEndpointDelete\" data-bs-tbdelete=\"".$v["id"]."\"><i class=\"bi bi-trash\"></i></button></span>";
                echo "</div>";
              echo "</div>";
            }
          }else{
            echo "<div class=\"endpoint row rounded my-2 p-3 border\">";
              echo "<div class=\"col-6\">";
                echo "<div class=\"row\">";
                  echo "<p>The issue is about your database connection.</p>";
                  echo "<p>If it's your first installation of LazyAPI, please refer to <a href='faq'>FAQ</a>.</p>";
                echo "</div>";
              echo "</div>";
            echo "</div>";
          }
        ?>
      </div>
    </div>

    <div class="modal fade" id="modalEndpointDelete" tabindex="-1" aria-labelledby="modalEndpointDeleteLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalEndpointDeleteLabel">Are you sur ?</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post">
            <div class="modal-body">
                <div class="mb-3">
                  <input type="text" class="form-control d-none" name="idEndpointToBeDeleted">
                  <p>You are going to delete a endpoint, Are you sur to do this action ? Everything about it gonna be deleted 😬.</p>
                </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn delete">Delete the endpoint</button>
            </div>
          </form>
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
    <script type="text/javascript">
      var modalEndpointDelete = document.getElementById('modalEndpointDelete')
      modalEndpointDelete.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget
        var idEndpoint = button.getAttribute('data-bs-tbdelete');
        var modalTitle = modalEndpointDelete.querySelector('.modal-title');
        var modalBodyInput = modalEndpointDelete.querySelector('.modal-body input');

        modalBodyInput.value = idEndpoint;
      })
    </script>
  </body>
</html>