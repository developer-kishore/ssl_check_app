<html>
<head>
    <title>SSL Expiration Application</title>
    <link rel = "icon" href ="ssl_logo.png" type = "image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
</head>
<body>
<br>
<?php
include 'db_conf.php';
$conn = OpenCon();

// Check user login or not
if(!isset($_SESSION['uname'])){
  header('Location: index.html');
}

function dateDiffInDays($date1, $date2) 
{
    // Calculating the difference in timestamps
    $diff = strtotime($date2) - strtotime($date1);
      
    // 1 day = 24 hours
    // 24 * 60 * 60 = 86400 seconds
    return abs(round($diff / 86400));
}
?>
<div class="container col-md-12">
<div class="float-right">
<label><?php echo $_SESSION['uname'];?></label>   
<a href="logout.php" type="button" class="btn btn-light">Logout</a> 
</div>
<h3 class="text-center">Register Your Domain to SSL Moniter</h3>
<br>
<form >
  <div class="row">
    <div class="col">
      <input type="text" class="form-control" placeholder="Enter Project name" name="project_name">
    </div>
    <div class="col">
      <input type="text" class="form-control" placeholder="Enter Domain name" name="domain_name">
    </div>
    <div class="col">
      <input type="email" class="form-control" placeholder="Enter Email" name="email">
    </div>
    <div class="col">
      <input type="number" class="form-control" placeholder="Days to Remind" name="days_to_remind">
    </div>
    <div class="col">
    <button type="submit" id="add_domain" class="btn btn-primary">Add Domain</button>
    </div>
  </div>
</form>
</div>
<br>
<div class="container col-md-12">
  <h3 class="text-center">SSL Available Domains</h3>
  <table class="table table-striped" id="ssl_table">
    <thead>
      <tr>
        <th>Project Name</th>
        <th>Domain Name</th>
        <th>Email</th>
        <th>Valid From</th>
        <th>Valid To</th>
        <th>Days Left</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $now = date('Y-m-d H:i:s');
        
        $sql = "SELECT * FROM domain_details";
        $result = mysqli_query($conn,$sql);
        if(mysqli_num_rows($result)){
        while($row = mysqli_fetch_assoc($result)){
        $daysLeft = dateDiffInDays($now, $row['valid_to']);
          
      echo'
        <tr>  
        <td>'.ucwords($row['project_name']).'</td>
        <td>'.$row['domain_name'].'</td>
        <td>'.$row['email'].'</td>
        <td>'.$row['valid_from'].'</td>
        <td>'.$row['valid_to'].'</td>';
        if($daysLeft <= $row['days_to_remind']){
          echo'
        <td style="color:red">'.$daysLeft.'</td>';
        }else if($daysLeft == 0){
          echo'
          <td style="color:red">SSL Expired</td>';
        }
        else{
          echo'
        <td>'.$daysLeft.'</td>';
        }
        echo'
        <td>
         <button class="btn btn-danger fas fa-trash delete" data-id='.$row['id'].'>Delete</button>
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
<script>
$(document).ready( function () {
    $('#ssl_table').DataTable();
});

//Find ssl certification
$(function () {
$('form').on('submit', function (e) {
  e.preventDefault();
  $.ajax({
    type: 'post',
    url: 'action_page.php',
    data: $('form').serialize(),
    success: function (data) {
      if(data == "url_failed"){
        alert("FAILED TO GET CERTIFICATE INFORMATION");
      }else if(data == "success"){
      window.location.reload();
      }else if(data == "Connection_Faild"){
        alert("Database Connection Faild");
      }
    }
  });
});
});

//delete record
$(document).ready(function(){
$('.delete').click(function(){
  var el = this;
  var deleteid = $(this).data('id');
  var confirmalert = confirm("Are you sure you want to Delete?");
  if (confirmalert == true) {
     $.ajax({
       url: 'action_page.php',
       type: 'POST',
       data: { act:"delete",id:deleteid },
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