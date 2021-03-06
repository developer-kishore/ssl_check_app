<?php
include 'db_conf.php';
include_once 'mail.php';
$conn = OpenCon();
if($conn){
    //echo "Connected Successfully";

function getcert($domain){
    $get = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
    $read = stream_socket_client("ssl://".$domain.":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $get);
    $cert = stream_context_get_params($read);
    return openssl_x509_parse($cert['options']['ssl']['peer_certificate']);
   }
//Domain name check   
function check_domain($domain){
 if ( gethostbyname($domain) != $domain ) {
  return "DNS_found";
 }
 else {
  return "NO_DNS_found";
 }
}   

   if($_POST['act'] == "delete"){
    if($_POST['id']){
        // Delete record
        $query = "DELETE FROM domain_details WHERE id=".$_POST['id'];
        mysqli_query($conn,$query);
        echo 1;
        exit;
      }else{
        echo 0;
        exit;
      }
   }else if($_POST['act'] == "expired_delete"){
    if($_POST['id']){
        // Delete record
        $query = "DELETE FROM expired_domain WHERE id=".$_POST['id'];
        mysqli_query($conn,$query);
        echo 1;
        exit;
      }else{
        echo 0;
        exit;
      }
   }
    
    $url = $_POST['domain_name'];//"https://www.seemymd.ca";
    $now = new DateTime('now', new DateTimeZone('UTC'));
    $check_domain = check_domain($url);
    if($check_domain == "DNS_found"){
    $certinfo = getcert($url);
    if (!$certinfo) {
        $insert = "INSERT INTO expired_domain (project_name,domain_name,email) VALUES ('$_POST[project_name]','$_POST[domain_name]','$_POST[email]')";
                if(mysqli_query($conn, $insert)) {
                   // echo "success";
                   $last_id = mysqli_insert_id($conn);
                }else{
                   echo "Error: " . $insert . "<br>" . mysqli_error($conn);
                }   
        if(!empty($_POST['email'])){
            $body = "Dears,<br>";
            $body .= "We would like to update you that the <b>".$_POST['project_name']."</b> located in the specified domain <br>";
            $body  .= "".$_POST['domain_name']." ???s SSL certificate was <b>expired.</b> <br>";
            $body .= "Kindly renewal to avoid the inconvenience. <br><br>";
            $body .= "Thanks.";
            $mail_status = send_mail($_POST['email'],$body);
            if($mail_status == "sent"){
                $sql = "UPDATE expired_domain SET email_sent = '1' WHERE id='".$last_id."'";

                if (mysqli_query($conn, $sql)) {
                  //echo "Record updated successfully";
                } else {
                  echo "Error updating record: " . mysqli_error($conn);
               }
                echo 'url_failed';
                exit;
            }
        }//check email
        echo 'url_failed';
    }
    else{
    $valid_from = new DateTime("@" . $certinfo['validFrom_time_t']);
    $valid_to = new DateTime("@" . $certinfo['validTo_time_t']);
   /// $diff = $now->diff($valid_to);
    //$daysLeft = $diff->invert ? 0 : $diff->days;
    //if ($daysLeft <= 15) $expiringSoon[] = $domain;
     
    $project_name = isset($_POST['project_name']) ? $_POST['project_name'] : ""; 
    $domain_name = isset($_POST['domain_name']) ? $_POST['domain_name'] : ""; 
    $email = isset($_POST['email']) ? $_POST['email'] : $_POST['email'];
    $days_to_remind = !empty($_POST['days_to_remind']) ? $_POST['days_to_remind'] : 2 ; 
    $from =  $valid_from->format('Y-m-d H:i:s');
    $to = $valid_to->format('Y-m-d H:i:s'); 

    $insert = "INSERT INTO domain_details (project_name,domain_name,email,valid_from,valid_to,days_to_remind) VALUES ('$project_name','$domain_name','$email','$from','$to','$days_to_remind')";
     if(mysqli_query($conn, $insert)) {
         echo "success";
     }else{
        echo "Error: " . $insert . "<br>" . mysqli_error($conn);
     }   
    // echo "Valid From: ".$valid_from->format('jS M Y'). ' (' . $valid_from->format('Y-m-d H:i:s') . ")<br>";
    //echo "Valid To: ".$valid_to->format('jS M Y'). ' (' . $valid_to->format('Y-m-d H:i:s') . ")<br>";
    //echo "Days Left:".$daysLeft;
    }
    //echo json_encode($data);
}//DNS CHeck
else{
    echo "NO_DNS_found";
}
}else{
        echo "Connection_Faild";
        }
        CloseCon($conn);
?>