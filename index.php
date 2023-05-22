<?php
session_start();

// db configs
try {
  $db = new PDO('mysql:host=localhost;dbname=test_todo', 'lamp', 'nepal123');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
  echo $e->getMessage();
}

// Check if the 'todo' table exists, and create it if not present
$tableCheck = $db->query("SHOW TABLES LIKE 'todo'");
if ($tableCheck->rowCount() == 0) {
  $createTable = $db->query("
    CREATE TABLE todo (
      id INT AUTO_INCREMENT PRIMARY KEY,
      item VARCHAR(255) NOT NULL,
      status INT NOT NULL DEFAULT 0
    )
  ");
  if ($createTable) {
    echo '<center><div class="alert alert-success placing" role="alert">
      Todo table created successfully
    </div></center>';
  } else {
    echo '<center><div class="alert alert-danger placing" role="alert">
      Error creating todo table: ' . $db->errorInfo()[2] . '
    </div></center>';
  }
}

if (isset($_POST['add'])) {
  $item = $_POST['item'];
  if (!empty($item)) {
    $newItem = $db->query("INSERT INTO todo (item, status) VALUES ('$item', 0) ");
    if ($newItem->rowCount() > 0 ) {
      echo '<center><div class="alert alert-success placing" role="alert">
                Item Added
            </div></center>';
    }
  }
}

if (isset($_GET['action'])) {
  $itemID = $_GET['item'];
  if ($_GET['action'] == 'done') {
    $updateStatus = $db->query("UPDATE todo SET status = 1 WHERE id = '$itemID' ");
    if ($updateStatus->rowCount() > 0) {
      echo '<center><div class="alert alert-info placing" role="alert">
                Item marked as done!
            </div></center>';
    }
  }
  elseif ($_GET['action'] == 'delete') {
    $deleteItem = $db->query("DELETE FROM todo WHERE id = '$itemID' ");
    if ($deleteItem->rowCount() > 0) {
        echo '<center><div class="alert alert-danger placing" role="alert">
                Item deleted!
            </div></center>';
    }
  }
}

?>
</head>
<body>
    <div class="container pt-5">
        <div class="row">
            <div class="col-sm-12 col-md-3"></div>
            <div class="col-sm-12 col-md-6">
                <div class="card middle-align">
                    <div class="card-header">
                        <p>Dynamic Todo Application</p>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= $_SERVER['PHP_SELF']; ?>">
                            <div class="mb-3">
                                <input type="text" class="form-control" name="item" placeholder="Add an item">
                            </div>
                            <input type="submit"  class="btn btn-dark"  name="add" value="Add item">
                        </form>
                        
                        <div class="todo-items">
                            <?php $items = $db->query("SELECT * FROM todo"); $c = 0; ?>
                            <?php if ($items->rowCount() < 1):?>
                                    <center>
                                        <img src="folder.png" width="50px" alt="Empty">
                                        <br>
                                        <span>Your list is empty</span>
                                    </center>
                            <?php endif;?>
                            <br>
                            <br>
                            <?php while($data = $items->fetchObject() ):?>
                                <div class="pt-2">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-6">
                                            <h4 class="item-heading <?= $data->status == 1 ? 'done' : ''; ?>"><?= $data->item; ?></h4>
                                        </div>
                                        <div class="col-sm-12 col-md-6">
                                            <a class="btn btn-outline-dark" href="?action=done&item=<?=$data->id;?>">Mark as done</a>
                                            <a class="btn btn-outline-danger" href="?action=delete&item=<?=$data->id;?>">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            <?php $c++; endwhile;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function(){
            $(".alert").fadeTo(5000, 500).slideUp(500, function(){
                $(".alert").slideUp(500);
            });
        });
    </script>
</body>
</html>
