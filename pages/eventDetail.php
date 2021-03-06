<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <!-- Fontawesome -->
    <link rel="stylesheet" href="../css/font-awesome.min.css">
    <link href="../css/bootstrap.css" rel="stylesheet">
    <link href="../css/main.css" rel="stylesheet">
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="../css/eventdetail.css">
    <link rel="stylesheet" type="text/css" href="../css/slick/slick.css"/>
    <link rel="stylesheet" type="text/css" href="../css/slick/slick-theme.css"/>
    <link rel="stylesheet" href="../assets/css/icomoon.css">
    <link href="../assets/css/animate-custom.css" rel="stylesheet">
    <link href="../css/header.css" rel="stylesheet">
    <link rel='stylesheet prefetch' href='http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>
    <link rel="stylesheet" href="../css/loginform.css">
    <link rel="stylesheet" href="../css/dropdownbtn.css">
    <link rel='stylesheet prefetch' href='https://octicons.github.com/components/octicons/octicons/octicons.css'>
    <link rel="stylesheet" type="text/css" href="styles.css"/>
</head>
<body>
<?php
/**
 * Created by PhpStorm.
 * User: nerminyildiz
 * Date: 4.04.2016
 * Time: PM 3:57
 * Function: Show the detail of an event
 */
session_start();
require("../php/dbinfo.php");
require "../php/zomato.php";


$get_string = $_SERVER['QUERY_STRING'];

parse_str($get_string, $get_array);


$name = $get_array['event'];
$name = addslashes($name);

$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die($conn->connect_error);
$query = "SELECT E.name as ename, E.descp as description, E.capacity as cap, V.name as vname, E.start as start, E.end as end, E.logo as logo,
 V.latitude as lat, V.longitude as lon, E.culture as culture, V.address1 as ad1, V.address2 as ad2, V.postal_code as postcode, V.city as city
  FROM Events E ,Venues V WHERE E.Venue = V.id and E.name='$name'"   ;
$result = $conn->query($query);
if (!$result) die($conn->error);

$event;
foreach($result as $row){
    //print_r($row);
    $event = $row;
    $eventname = $row['ename'];
    $descp = $row['description'];
    $cap = $row['cap'];
    $eventstart = $row['start'];
    $eventend = $row['end'];
    $venue = $row['vname'];
    $pic = $row['logo'];
    $address = $row['ad2']. ' '. $row['ad1']. ' '. $row['city'];
    $pcode = $row['postcode'];
    $lati = $row['lat'];
    $long = $row['lon'];
    $culture = $row['culture'];
    break;
}



$zmt_client = new zomato('c31173bf9d57bbc6aeee69445019a82f');

if ($culture == 'Chinese OR China') {
    $culture = '25';
}
else if ($culture == 'Greek OR Greece') {
    $culture = '156';
}
else if ($culture == 'Turkish OR Turkey') {
    $culture = '142';
}
else if ($culture == 'Indian OR India') {
    $culture = '148';
}
else if ($culture == 'Italian OR Italy') {
    $culture = '55';
}

//Set the options for searching events
$info = array(
    'data' => array(
        'lat' => $lati,
        'lon' => $long,
        'cuisines' => $culture,
        'radius'=>'5000',
        'sort' => 'rating'
    )
);

$result = $zmt_client->restaurants('search', $info)["restaurants"];

//$json = file_get_contents('http://api.openweathermap.org/data/2.5/weather?lat='.$lati.'&lon='.$long.'&dt='.$eventstart.'&units=metric&APPID=e3756cc5200d05460a0df2833f10214a');
$json = file_get_contents('http://api.openweathermap.org/data/2.5/forecast/daily?lat='.$lati.'&lon='.$long.'&count=7&units=metric&APPID=e3756cc5200d05460a0df2833f10214a');
//print_r($json);
//$json  = file_get_contents('http://api.openweathermap.org/data/2.5/weather?lat=-37.814396&lon=144.963616&units=metric&APPID=e3756cc5200d05460a0df2833f10214a');
$data  = json_decode($json,true);


for($i=0; $i<7; $i++ ) {
    $dt = $data['list'][$i]['dt'];
    $tarih = date('d/m/Y H:i:s', $dt);

    if (substr($tarih, 0, 2) == substr($eventstart, 8, -9) && substr($tarih, 3, 2) == substr($eventstart, 5, -12)) {
        $hava = $data['list'][$i]['temp']['day'] . ' °C';
        $icon = $data['list'][$i]['weather'][0]['icon'];
        $img = 'http://openweathermap.org/img/w/' . $icon . '.png';
        $i = 7;
    } else {
        $hava = 'not available';
        $img = '../images/weather_na.png';

    }
}

?>
<div style="width: 100%">
    <div id="popupbox" class="module form-module popuplogin">
        <div class="toggle">
            <a class="fa fa-times" href="javascript:login('hide');"></a>
            <div class="tooltip">Click Me</div>
        </div>
        <div class="form">
            <h2>Login to your account</h2>
            <form method="post" name="login" action="../php/login.php">
                <input type="text" name="username" id="userid" required="required" placeholder="Username"/>
                <input type="password" name="password" id="passid" required="required" placeholder="Password"/>
                <button type="submit" name="submit" id="submit" value="submit">Login</button>
            </form>
        </div>
        <div class="form">
            <h2>Create an account</h2>
            <form>
                <input type="text" placeholder="Username"/>
                <input type="password" placeholder="Password"/>
                <input type="email" placeholder="Email Address"/>
                <input type="tel" placeholder="Phone Number"/>
                <button>Register</button>
            </form>
        </div>
        <div class="cta"><a href="registration.php">Sign Up</a></div>
    </div>
</div>




<!--Fixed Navigation
==================================== -->
<header id="navigation" class="nav navbar-static-top">
    <div class="container">

        <div class="navbar-header">
            <!-- responsive nav button -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-menu">
                <h3><i class="fa fa-bars"></i></h3>
            </button>
            <!-- /responsive nav button -->

            <!-- logo -->
            <a class="navbar-brand" href="../index.php">
                <img src="../images/logo.png" width="112" height="36" alt="Logo">
            </a>
            <!-- /logo -->

        </div>

        <!-- main nav -->
        <nav id="main-menu" class="collapse navigation navbar-collapse navbar-right"
             style="z-index:1000; position: relative" role="navigation">
            <ul class="nav navbar-nav hidden-xs ">
                <li><a href="../pages/organize.php">
                        <div style="border-style: solid; border-color: gainsboro; border-width: thin">
                            &nbsp&nbsp Organize an event &nbsp&nbsp
                        </div>
                    </a>
                </li>
                <li id="admin2" style="width: 108px;">
                    <a href="#">Service</a>
                    <div id="menu2" class="menu">
                        <div class="arrow"></div>
                        <a href="event.php?clt=test">Event <span
                                class="icon octicon octicon-list-ordered"></span></a>
                        <a href="community.php">Community <span
                                class="icon octicon octicon-organization"></span></a>
                        <a href="story.php">Story <span class="icon octicon octicon-squirrel"></span></a>
                    </div>
                </li>
                <li id="admin1" style="width: auto;">
                    <?php


                    if (isset($_SESSION['login_username'])) {

                        echo '<a href="#">' . $_SESSION['login_username'] . ' </a>';
                        echo '<div id="menu1" class="menu">
                            <div class="arrow"></div>
                            <a href="#">My Profile <span class="icon octicon octicon-person"></span></a>
                            <a href="myevent.php">My Events <span class="icon octicon octicon-tasklist"></span></a>
                            <a href="mystory.php">My Stories <span class="icon octicon octicon-rocket"></span></a>
                            <a href="poststory.php">Post Story<span class="icon octicon octicon-pencil"></span></a>
                            <a href="../php/logout.php">Logout <span class="icon octicon octicon-sign-out"></span></a>
                        </div>';
                    } else {
                        echo '<a href="javascript:login(\'show\');">login </a>';
                    }
                    ?>

                </li>
            </ul>

            <ul id="mobnav" class="nav navbar-nav visible-xs">
                <?php
                if (isset($_SESSION['login_username'])) {
                    echo '<li><a href="#">' . $_SESSION['login_username'] . '<i class="fa fa-user pull-right" aria-hidden="true"></i></a></li>';

                    ?>
                    <li><a href="../index.php">Home<i class="fa fa-home pull-right" aria-hidden="true"></i></a></li>
                    <li><a href="event.php?clt=test">Event<i class="fa fa-list-ol pull-right"
                                                                   aria-hidden="true"></i></a></li>
                    <li><a href="community.php">Community<i class="fa fa-university pull-right"
                                                                  aria-hidden="true"></i></a></li>
                    <li><a href="story.php">Story<i class="fa fa-paper-plane pull-right"
                                                          aria-hidden="true"></i></a></li>

                    <?php
                    echo '<li><a href="../php/logout.php">Logout<i class="fa fa-sign-out pull-right" aria-hidden="true"></i></a></li>';

                } else {
                    echo '<li><a href="../index.php">Home<i class="fa fa-home pull-right" aria-hidden="true"></i></a></li>';
                    echo '<li><a href="event.php?clt=test">Event<i class="fa fa-list-ol pull-right" aria-hidden="true"></i></a></li>';
                    echo '<li><a href="community.php">Community<i class="fa fa-university pull-right" aria-hidden="true"></i></a></li>';
                    echo '<li><a href="pages/story.php">Story<i class="fa fa-paper-plane pull-right" aria-hidden="true"></i></a></li>';
                    echo '<li><a href="javascript:login(\'show\');">Login<i class="fa fa-sign-in pull-right" aria-hidden="true"></i></a></li>';
                }
                ?>
            </ul>
        </nav>
        <!-- /main nav -->


    </div>


</header>


<div id="" class="eventhead" style="background-image: url('<?=$pic?>');">
    <div class="image-overlay"">
        <div class="eventname">
            <h2> <?=$eventname?> </h2>
            <br>
            <?=$venue?><br><br>
            <a href="../php/calendar.php" style="color: black"><img src="../images/GoogleCalendar.png"></a>
        </div>
    </div>

</div>
<div class="container">
    <div class="descp" style="margin-left: 90px; margin-right: 50px;">
        <?=$descp?>
    </div>
    <div class="detail" style="margin-left: 0px">
        <b>Address</b>
        <br>
        <?=$address?>
        <br>
        <br>
        <b>Postal Code</b>
        <br>
        <?=$pcode?>
        <br>
        <br>
        <b>Venue</b>
        <br>
        <?=$venue?>
        <br>
        <br>
        <b>Start Time</b>
        <br>
        <?=substr($eventstart, 8,-9)?>
        /
        <?=substr($eventstart, 5,-12)?>
        /
        <?=substr($eventstart, 0,-15)?>
        <br>
        <?=substr($eventstart, 11,-3)?>
        <br>
        <br>
        <b>End Time</b>
        <br>
        <?=substr($eventend, 8,-9)?>
        /
        <?=substr($eventend, 5,-12)?>
        /
        <?=substr($eventend, 0,-15)?>
        <br>
        <?=substr($eventend, 11,-3)?>
        <br>
        <br>
        <b>Capacity</b>
        <br>
        <?=$cap.' people'?>
        <br>
        <br>
        <b>Weather</b><br>
        <img src="<?=$img?>" style="width: 75px">
        <?=$hava?><br>

    </div>

    <div id="clear"></div>
    <div style="width: 90%; margin-left: 5%; margin-top: 20px">
        <h3 style="color: black; font: 'Lato', sans-serif">Restaurant Nearby</h3>
    <div class="sliderc">

        <?php
        $count = 0;
        foreach($result as $row) {
            if($count >= 6){
                break;
            }
            ?>
            <div>
                <a style="display:block" href="<?= $result[$count]->restaurant->url ?>">
                <div id="" class="slick-img"
                     style="background-image: url('<?= $result[$count]->restaurant->featured_image ?>');">
                </div>
                <h3 href="<?= $result[$count]->restaurant->url ?>"> <?= $result[$count]->restaurant->name ?> </h3>
                    </a>
                <p> Rating: <?= $result[$count]->restaurant->user_rating->aggregate_rating ?>
                <br>
                    Address: <?= $result[$count]->restaurant->location->address ?>
                </p>
            </div>
            <?php
            $count++;
        }
        ?>
    </div>
        </div>
</div>

<section class="rowfooter breath container-fluid" style="padding: 0px">
    <div class="row">
        <div class="col-md-12"
             style="background-color: #adadad; color:black; text-align: center; height: 60px; margin-top: 30px">
            <p><br>© 2016 Dream Builders. All Rights Reserved</p>
        </div>
    </div>
</section>


<script type="text/javascript" src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../css/slick/slick.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $('.sliderc').slick({
// dots: true,
            infinite: true,
            speed: 300,
            slidesToShow: 3,
            slidesToScroll: 1,
            responsive: [{
                breakpoint: 1024,
                settings: {
                    slidesToShow: 6,
                    slidesToScroll: 1,
                }

            }, {
                breakpoint: 800,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 2,
                    dots: true,
                    infinite: true,

                }


            }, {
                breakpoint: 600,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 2,
                    dots: true,
                    infinite: true,

                }
            }, {
                breakpoint: 480,
                settings: {
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    dots: true,
                    infinite: true,
                    autoplay: true,
                    autoplaySpeed: 2000,
                }
            }]
        });


    });
</script>
</body>
<script type="text/javascript" src="../js/loginform.js"></script>
<script src="../js/dropdownbtn.js"></script>
</html>
