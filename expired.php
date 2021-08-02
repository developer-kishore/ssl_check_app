<?php
include 'db_conf.php';
$conn = OpenCon();
if($conn){

    // Check user login or not
if(!isset($_SESSION['uname'])){
    header('Location: index.html');
  }
  
    ?>
    <html>
<head>
    <title>SSL Expiration Application</title>
    <link rel = "icon" href ="pngwing.com.png" type = "image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
</head>
<body>
    <br>
<div class="container">
<div class="float-left">
  <a  href="index.php" type="button" class="btn btn-info fa fa-chevron-left"> Back</a> 
  </div>
<div class="float-right">
<label class="fa fa-user"> <?php echo ucwords($_SESSION['uname']);?></label> |  
<a href="logout.php" type="button" class="btn btn-light fa fa-sign-out">Logout</a> 
</div>
  <h3 class="text-center">SSL Expired Domains</h3>
  <br>
  <table class="table table-striped" id="expired_table">
    <thead>
      <tr>
        <th>Project Name</th>
        <th>Domain Name</th>
        <th>Email</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $now = date('Y-m-d H:i:s');
        
        $sql = "SELECT * FROM expired_domain";
        $result = mysqli_query($conn,$sql);
        if(mysqli_num_rows($result)){
        while($row = mysqli_fetch_assoc($result)){
        $project_name = ucwords($row['project_name']);
        $domain_name = $row['domain_name'];
      echo'
        <tr>  
        <td>'.$project_name.'</td>
        <td>'.$domain_name.'</td>
        <td>'.$row['email'].'</td>
        <td>'.$row['status'].'</td>
        <td>
         <button class="btn btn-danger fa fa-trash delete" data-id='.$row['id'].'></button>
        </td>
        </tr>';
        }
        }else{
          echo "<tr>No results found</tr>";
        }
        ?>
      
    </tbody>
  </table>
</div>
</body>
</html>
<?php } ?>
<script>
$(document).ready( function () {
    $('#expired_table').DataTable();
});

$(document).ready(function(){
$('.delete').click(function(){
  var el = this;
  var deleteid = $(this).data('id');
  var confirmalert = confirm("Are you sure you want to Delete?");
  if (confirmalert == true) {
     $.ajax({
       url: 'action_page.php',
       type: 'POST',
       data: { act:"expired_delete",id:deleteid },
       success: function(response){
         if(response == 1){
     // Remove row from HTML Table
     $(el).closest('tr').css('background','tomato');
     $(el).closest('tr').fadeOut(800,function(){
        $(this).remove();
     });
         }else{
     alert('Invalid ID.');
         }

       }
     });
  }
});
});
</script>