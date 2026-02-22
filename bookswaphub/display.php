<?php
include "Model.php";
    $obj = new Model();
    // Check if form is submitted for update
if (isset($_POST['update'])) {
    $obj->updateRecord($_POST, $_FILES);
}
    if (isset($_GET['deleteid'])) {
        $delid=$_GET['deleteid'];
        $obj->deletRecord($delid);
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Exchange Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
table {
  border-collapse: collapse;
  width: 100%;
}

th, td {
  text-align: left;
  padding: 8px;
  
}

tr:nth-child(even) {background-color: #f2f2f2;}
h2{
  text-align:center;
  background-color:grey;
  color:#000;
  
}
button {
  background-color:grey; /* Green */
  border: none;
  border-radius:5px;
  color: red;
  padding: 15px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 3px 2px;
  cursor: pointer;
}
input.delete{
  background-color:grey; /* Green */
  border: none;
  border-radius:5px;
  color: red;
  padding: 15px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 3px 2px;
  cursor: pointer;
}

</style>
      </head>
  <body>
  <?php
$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
if ($msg === 'ups') {
    echo '<div class="alert alert-primary" role="alert">
    Record updated successfully ..
    </div>';
}
?>
<?php
$msg = isset($_GET['msg']) ? $_GET['msg'] : null;
if ($msg === 'del') {
    echo '<div class="alert alert-primary" role="alert">
    Record deleted successfully ..
    </div>';
}
?>
    <h2><u>Books_crud_details </u></h2>
  <table border="1">
<tr>
    <th>ID</th>  
    <th>bookcondition</th>
    <th>isbn</th>
    <th>title</th>
    <th>price</th>
    <th>final_price</th>
    <th>type</th>
    <th>category</th>
    <th>author</th>
    <th>year</th>
    <th>photo</th> 
    <th>book_description</th> 
    <th>operation</th>

  </tr>

  <?php
   $data = $obj->displayRecord();
   $sno=1;
    foreach($data as $value){
        ?>
       <tr>
       <td><?php echo $sno++; ?></td>
       <td><?php echo $value['book_condition'] ?></td>
       <td><?php echo $value['isbn'] ?></td>
       <td><?php echo $value['title'] ?></td>
       <td><?php echo $value['price'] ?></td>
       <td><?php echo $value['final_price'] ?></td>
       <td><?php echo $value['type'] ?></td>
       <td><?php echo $value['category'] ?></td>
       <td><?php echo $value['author'] ?></td>
       <td><?php echo $value['year'] ?></td>
       <td><?php echo "<img src=".$value['photo']." width='150px' height='150px'>" ?></td>
       <td><?php echo $value['book_description'] ?></td>
       <td>
        <a href="form_update.php?editid=<?php echo $value['book_ID'];?>" class="btn btn-info" >Edit</a>
        <a href="display.php?deleteid=<?php echo $value['book_ID'];?>" class="btn btn-danger" >Delete</a>

       <!-- <a href=''><u><input type='submit' value='delete'
       class='delete' onclick='return checkdelete()'></u>
       </a> -->
        </td>
      </tr>
        <?php
    }
      ?>

    



</table>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
