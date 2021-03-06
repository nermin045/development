<?php
require "dbinfo.php";
include "session.php";
$path = '';
$data = file_get_contents("php://input");
parse_str($data, $get_array);

if(isset($_FILES['image'])){
    $errors= array();
    $file_name = uniqid(). $_FILES['image']['name'];
    $file_size =$_FILES['image']['size'];
    $file_tmp =$_FILES['image']['tmp_name'];
    $file_type=$_FILES['image']['type'];
    $path = __DIR__ . "/photos/" . basename($file_name);
    $pic = "../php/photos/". basename($file_name);;

    if($file_size > 5097152){
        $errors[]='File size must be no more than 5 MB';
    }

    if(empty($errors)==true){
        move_uploaded_file($file_tmp, $path);
        header("Location:../pages/mystory.php");
    }else{
        print_r($errors);
    }
}

$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die($conn->connect_error);

//$title = $_POST["title"];
//$content = $_POST["content"];
//$culture = $_POST["culture"];

$title = $get_array['title'];
$content = $get_array['content'];
$culture = $get_array['culture'];

$image = $pic;
$time = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));

$user_ip = getenv('REMOTE_ADDR');
$geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$user_ip"));
$latitude = $geo["geoplugin_latitude"];
$longitude = $geo["geoplugin_longitude"];
$user = $_SESSION['login_username'];


$sql = "INSERT INTO Story(title, content, image, latitude, longtitude, postdate, culture, username) " .
"VALUES ('$title', '$content', '$image', '$latitude','$longitude', $time, '$culture', '$user')";

$result = $conn->query($sql);

if (!$result) {
    echo "INSERT failed: $sql<br>" .
        $conn->error . "<br><br>";
}

?>
