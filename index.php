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
<?php
include 'db_conf.php';
include_once 'mail.php';
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
<div class="container">
<div class="float-right">
<label class="fa fa-user"> <?php echo ucwords($_SESSION['uname']);?></label>   
<a href="logout.php" type="button" class="btn btn-light fa fa-sign-out">Logout</a> 
</div>
<h3 class="text-center">Register Your Domain for SSL Expire Monitoring</h3>
<br>
<form >
  <div class="row">
    <div class="col">
      <input type="text" class="form-control" placeholder="Enter Project name" name="project_name" required>
    </div>
    <div class="col">
      <input type="text" class="form-control" placeholder="Enter Domain name" name="domain_name" required>
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
<div class="container">
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
        $project_name = ucwords($row['project_name']);
        $domain_name = $row['domain_name'];
      echo'
        <tr>  
        <td>'.$project_name.'</td>
        <td>'.$domain_name.'</td>';
        if(!empty($row['email']) && $row['email_sent'] == 1){
          echo'
          <td style="color:green">'.$row['email'].'</td>';
        }
        else{
          echo'
          <td>'.$row['email'].'</td>';
        }
        echo'
        <td>'.$row['valid_from'].'</td>
        <td>'.$row['valid_to'].'</td>';
        if($daysLeft <= $row['days_to_remind']){
          echo'
        <td style="color:red">'.$daysLeft.'</td>';
        if(!empty($row['email']) && $row['email_sent'] == 0){
          $body = "Dears,<br>";
          $body .= "We would like to update you that the <b>$project_name</b> located in the specified domain <br>";
          $body  .= "$domain_name ’s SSL certificate is going to be expired in <b>$daysLeft</b> days. <br>";
          $body .= "Kindly renewal to avoid the inconvenience. <br><br>";
          $body .= "Thanks.";
         $mail_status = send_mail($row['email'],$body);
         if($mail_status == "sent"){
          $sql = "UPDATE domain_details SET email_sent = '1' WHERE id='".$row['id']."'";

         if (mysqli_query($conn, $sql)) {
           //echo "Record updated successfully";
         } else {
           echo "Error updating record: " . mysqli_error($conn);
        }
        }
        }
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
      }else if(data == "NO_DNS_found"){
        alert("Domain name not available");
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