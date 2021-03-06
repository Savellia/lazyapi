<?php
  require "config.php";
  require "core/engine.php";
  
  if(!accessChecker()){
    header("Location: /");
  }

  $insert = null;

  if(isset($_POST["name"]) && isset($_POST["description"]) && isset($_POST["method"]) && isset($_POST["path"]) && isset($_POST["function"])){
    $name = htmlentities(addslashes($_POST["name"]));
    $description = htmlentities(addslashes($_POST["description"]));
    $method = htmlentities(addslashes($_POST["method"]));
    $path = htmlentities(addslashes($_POST["path"]));
    $function = htmlentities(addslashes($_POST["function"]));

    $state = $_POST["state"] ? 1 : 0;

    if(isset($_POST["parameter-name"]) && isset($_POST["parameter-type"]) && count($_POST["parameter-name"]) == count($_POST["parameter-type"])){
      foreach ($_POST["parameter-name"] as $key => $value) {
        $_POST["parameter-name"][$key] = htmlentities(addslashes($_POST["parameter-name"][$key]));
        $_POST["parameter-type"][$key] = htmlentities(addslashes($_POST["parameter-type"][$key]));
      }
      $parametersName = $_POST["parameter-name"];
      $parametersType = $_POST["parameter-type"];
    }else{
      $parametersName = false;
      $parametersType = false;
    }

    if(isset($_POST["domain-name"])){
      foreach ($_POST["domain-name"] as $key => $value) {
        $_POST["domain-name"][$key] = htmlentities(addslashes($_POST["domain-name"][$key]));
      }
      $domainsName = $_POST["domain-name"];
    }else{
      $domainsName = false;
    }

    $insert = insertEndpoint($name, $description, $method, $path, $function, $state, $parametersName, $parametersType, $domainsName);
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
    <title>New endpoint</title>
  </head>
  <body>
    <div class="container">
      <div class="my-2">
        <div class="row">
          <h1 class="display-5 p-0">New endpoint</h1>
        </div>
        <div class="row">
          <?php
            if($insert){
              echo "<button class='goto rounded px-2 py-1 mb-2'>The endpoint has been created ????</button>";
            }elseif($insert === false){
              echo "<button class='delete rounded px-2 py-1 mb-2'>An error has been occured ????</button>";
            }
          ?>
        </div>
        <div class="row">
          <a href="panel" class="p-0"><button class="back rounded px-2 py-1 col-auto"><i class="bi bi-arrow-left"></i> Back</button></a>
        </div>
        <form action="" method="post">
          <div class="endpoint row rounded my-2 p-3 border">
            <div class="col-6">
              <div class="row">
                <h3>Information</h3>
              </div>

              <div class="mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="myEndpoint" required>
              </div>
              <div class="mb-3">
                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="description" name="description" placeholder="The best endpoint ever." required>
              </div>
              <div class="mb-3">
                <label for="method" class="form-label">Method <span class="text-danger">*</span></label>
                <select class="form-select" id="method" name="method" required>
                  <option value="GET">GET</option>
                  <option value="POST">POST</option>
                </select>
              </div>
              <div class="form-floating mb-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="state" id="on">
                  <label class="form-check-label" for="on">
                   Make this endpoint available.
                  </label>
                </div>
              </div>
            </div>

            <div class="col-6">
              <div class="row parameters-list">
                <div class="col-auto">
                  <h3>Parameters</h3>
                </div>

                <div class="col-auto">
                  <button type="button" class="add param rounded px-2 py-1 col-auto my-1"><i class="bi bi-plus-circle"></i> New parameter</button>
                </div>
              </div>
              <div class="row">
                <div class="col-auto">
                  <span class="alert-no-parameters"><i>No parameters for this endpoint.</i></span>
                </div>
              </div>

              <div class="row domains-list mt-2">
                <div class="col-auto">
                  <h3>Security</h3>
                </div>

                <div class="col-auto">
                  <button type="button" class="add dom rounded px-2 py-1 col-auto my-1"><i class="bi bi-plus-circle"></i> New domain</button>
                </div>
                <div class="row">
                  <div class="col-auto">
                    <span class="alert-no-domains"><i>All domains allowed for this endpoint.</i></span>
                  </div>
                </div>
              </div>

              <div class="row data-management mt-2">
                <div class="col-auto">
                  <h3>Data management</h3>
                </div>

                <div class="mb-3">
                  <label for="path" class="form-label">Path to the file <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="path" name="path" placeholder="/path/to/file.extension" required>
                  <small class="form-text">Current path: <?php echo substr($_SERVER["REQUEST_URI"],0, strrpos($_SERVER["REQUEST_URI"], "/"))."/"; ?></small>
                </div>

                <div class="mb-3">
                  <label for="function" class="form-label">Function name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="function" name="function" placeholder="className::functionName" required>
                  <p class="mb-0"><small class="form-text">Do not insert parameters with the function name. You have to add the class if needed.</small></p>
                  <p><small class="form-text">Preview:</small> <small class="form-text preview-function">a</small>(<small class="form-text preview-parameters">b</small>)</p>
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="row">
                <div class="col-auto">
                  <button type="submit" class="goto rounded px-2 py-1"><i class="bi bi-check2"></i> Create</button>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script type="text/javascript">
      $( document ).ready(function() {
        callPreview();
      });
      $( document ).on( "click", ".add.param", function() {
        var parameter = " <div class='row parameter mb-3'> <div class='col-auto'> <label class='form-label'>Name</label> <input type='text' class='form-control parameter-name' name='parameter-name[]'> </div><div class='col-auto'> <label class='form-label'>Type</label> <select class='form-select parameter-type' name='parameter-type[]'> <option value='int'>int</option> <option value='numeric'>numeric</option> <option value='float'>float</option> <option value='string'>string</option> <option value='bool'>bool</option> <option value='array'>array</option> <option value='object'>object</option> <option value='unsecured'>unsecured</option> </select> </div><div class='col-auto goto-trash'> <button class='delete param rounded px-2 py-1' data-bs-toggle='tooltip' data-bs-placement='top' title='Delete the parameter'><i class='bi bi-trash'></i></button> </div></div>";
        $( parameter ).appendTo( ".parameters-list" );
        $( ".alert-no-parameters" ).hide();
      });
      $( document ).on( "click", ".delete.param", function() {
        $( this ).parent().parent().remove();

        if($( ".parameter" ).length == 0){
          $( ".alert-no-parameters" ).show();
        }
      });
      $( document ).on( "click", ".add.dom", function() {
        var domain = " <div class='row domain mb-3'> <div class='col-auto'> <label class='form-label'>Domain</label> <input type='text' class='form-control domain-name' name='domain-name[]'> </div><div class='col-auto goto-trash'> <button class='delete domain rounded px-2 py-1' data-bs-toggle='tooltip' data-bs-placement='top' title='Delete the domain'><i class='bi bi-trash'></i></button> </div></div>";
        $( domain ).appendTo( ".domains-list" );
        $( ".alert-no-domains" ).hide();
      });
      $( document ).on( "click", ".delete.domain", function() {
        $( this ).parent().parent().remove();

        if($( ".domain" ).length == 0){
          $( ".alert-no-domains" ).show();
        }
      });
    </script>
    <script type="text/javascript">
      $( document ).on( "input", "#function, .parameter-name", function() {
        callPreview();
      });
      function callPreview(){
        var tmp = "";
        $( ".parameter-name" ).each(function( index ) {
          tmp += $( this ).val()+", ";
        });
        $("small.preview-function").text($("#function").val());
        $("small.preview-parameters").text(tmp.slice(0,-2));
      }
    </script>
  </body>
</html>