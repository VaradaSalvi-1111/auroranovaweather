<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.html');
    exit;
}
?>
<?php
$apiKey = "04c42c4ff9da1fc464dfd08f6f8e0742";
$location = isset($_POST['loc']) ? urlencode($_POST['loc']) : 'Pune';
function fetchData($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL for testing
    $response = curl_exec($ch);
    if ($response === false) {
        die("cURL Error: " . curl_error($ch));
    }
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($code != 200) {
        die("HTTP CODE: $code<br>RESPONSE:<br>$response");
    }
    return $response;
}
$apiUrl = "https://api.openweathermap.org/data/2.5/weather?q={$location}&limit=1&appid={$apiKey}&units=metric";
$weatherData = json_decode(fetchData($apiUrl), true);
$temp = $weatherData['main']['temp'] ?? '--';
$humidity = $weatherData['main']['humidity'] ?? '--';
$pressure = $weatherData['main']['pressure'] ?? '--';
$windSpeed = $weatherData['wind']['speed'] ?? '--';
if ($temp !== '--' && $humidity !== '--') {
    $dewPoint = round($temp - ((100 - $humidity) / 5), 1);
} else {
    $dewPoint = '--';
}

$weatherCondition = $weatherData['weather'][0]['description'] ?? '--';
$icon = $weatherData['weather'][0]['icon'] ?? '01d';
$lat = $weatherData['coord']['lat'] ?? 0;
$lon = $weatherData['coord']['lon'] ?? 0;
$timezone_offset = $weatherData['timezone'] ?? 0;

$day = gmdate('l', time() + $timezone_offset);
$date = gmdate('jS F, Y', time() + $timezone_offset);
$time = gmdate('h:i:s A', time() + $timezone_offset);
$sunrise = isset($weatherData['sys']['sunrise']) ? gmdate("H:i", $weatherData['sys']['sunrise'] + $timezone_offset) : '--';
$sunset  = isset($weatherData['sys']['sunset'])  ? gmdate("H:i", $weatherData['sys']['sunset'] + $timezone_offset) : '--';

$aqiUrl = "https://api.openweathermap.org/data/2.5/air_pollution?lat={$lat}&lon={$lon}&appid={$apiKey}";
$aqiData = json_decode(fetchData($aqiUrl), true);

$aqiIndex = $aqiData['list'][0]['main']['aqi'] ?? '--';

function aqiDescription($aqi){
    return match($aqi){
        1 => 'Good',
        2 => 'Fair',
        3 => 'Moderate',
        4 => 'Poor',
        5 => 'Very Poor',
        default => '--'
    };
}
$aqiDesc = aqiDescription($aqiIndex);

// UV index
$apiUrl = "https://api.openweathermap.org/data/2.5/weather?q={$location}&appid={$apiKey}&units=metric";
$oneCallData = json_decode(fetchData($apiUrl), true);

$uvIndex = $oneCallData['current']['uvi'] ?? '--';
function uvDescription($uv){
    if ($uv < 3) return 'Low';
    elseif ($uv < 6) return 'Moderate';
    elseif ($uv < 8) return 'High';
    elseif ($uv < 11) return 'Very High';
    else return 'Extreme';
}
$uvDesc = uvDescription($uvIndex);

$forecastUrl = "https://api.openweathermap.org/data/2.5/forecast?q={$location}&appid={$apiKey}&units=metric";
$forecastData = json_decode(fetchData($forecastUrl), true);


$hourlyForecast = $forecastData['list'] ?? [];
$timezone_offset = $forecastData['city']['timezone'] ?? 0;
$chartHours = [];
$chartTemps = [];

foreach ($hourlyForecast as $hour) {
    $chartHours[] = gmdate('H:i', $hour['dt'] + $timezone_offset);
    $chartTemps[] = $hour['main']['temp'];
}
// Group daily forecast
$dailyForecast = [];
foreach($hourlyForecast as $f) {
    $dayName = gmdate('D', $f['dt'] + $timezone_offset);
    if(!isset($dailyForecast[$dayName])) {
        $dailyForecast[$dayName] = [
            'dt' => $f['dt'],
            'day' => $dayName,
            'temp_min' => $f['main']['temp_min'],
            'temp_max' => $f['main']['temp_max'],
            'icon' => $f['weather'][0]['icon']
        ];
    } else {
        $dailyForecast[$dayName]['temp_min'] = min($dailyForecast[$dayName]['temp_min'], $f['main']['temp_min']);
        $dailyForecast[$dayName]['temp_max'] = max($dailyForecast[$dayName]['temp_max'], $f['main']['temp_max']);
    }
}
$dailyForecast = array_slice(array_values($dailyForecast),0,10);
$defaultIcon ='images/weather/snowfall.png';
$snowMap = [
    600 => 'images/weather/snowfall.png',
    601 => 'images/weather/snowfall.png',
    602 => 'images/weather/snowfall.png',
    611 => 'images/weather/snowfall.png',
    612 => 'images/weather/snowfall.png',
    615 => 'images/weather/snowfall.png',
    616 => 'images/weather/snowfall.png',
    620 => 'images/weather/snowfall.png',
    621 => 'images/weather/snowfall.png',
    622 => 'images/weather/snowfall.png',
];
$iconMap = [
    '01d' => 'images/weather/sunny.png',      // clear day
    '01n' => 'images/weather/night.png',      // clear night
    '02d' => 'images/weather/cloudy_day.png', // few clouds day
    '02n' => 'images/weather/cloudy_night.png',
    '03d' => 'images/weather/scattered_cloudy.png',      // scattered clouds
    '03n' => 'images/weather/scattered_cloudy.png',
    '04d' => 'images/weather/scattered_cloudy.png',      // broken clouds / overcast
    '04n' => 'images/weather/scattered_cloudy.png',
    '09d' => 'images/weather/rain.png',       // rain
    '09n' => 'images/weather/rain.png',
    '10d' => 'images/weather/sunrain.png',       // rain with sun
    '10n' => 'images/weather/sunrain.png',
    '11d' => 'images/weather/thunderstorm.png',      // thunderstorm
    '11n' => 'images/weather/thunderstorm.png',
    '13d' => 'images/weather/snowfall.png',       // snowfall
    '13n' => 'images/weather/snowfall.png',
    '50d' => 'images/weather/haze.png',        // mist / haze / fog
    '50n' => 'images/weather/haze.png',
    
];

$weatherId = $weatherData['weather'][0]['id'] ?? 0;
$iconCode  = $weatherData['weather'][0]['icon'] ?? '01d';

$iconFile = $snowMap[$weatherId] ?? $iconMap[$iconCode] ??$defaultIcon ;
if(!file_exists($iconFile))
{
    $iconFile=$defaultIcon;
}

$geoUrl = "https://api.openweathermap.org/geo/1.0/direct?q={$location}&limit=1&appid={$apiKey}";
$geoData = json_decode(fetchData($geoUrl), true);
$state=$geoData[0]['state'] ?? ' ';
$country=$geoData[0]['country'] ?? ' ';

?>

<!DOCTYPE html>
<html>
    <head>
        <title>home page</title>
         <meta charset="UTF-8">
         <meta name="viewport" content="width=device-width, initial-scale=1.0">
         
    <style>
         *{
            box-sizing: border-box;
        }
        img 
        {
            filter:brightness(1.5);
        } 
        body{
            background: radial-gradient(circle at center, #0a1d42 0%, #050a1b 100%);
            background-repeat: no-repeat;
            background-size: 100% 100%;
            background-attachment: fixed;
            background-position: center;
          margin:1px;
          padding:1px;
          height: 100%;
          font-family: Verdana, Geneva, Tahoma, sans-serif;
          color: #ffffff;
           }
        
        a{
            font-family:'Pacifico';
            font-size: 120%;
            color: white;
            text-decoration: none;
        }
       i{
        color:white;
        font-size:275%;
        font-family:'Pacifico';
       
       }
       .container
       {
        width:100%;
        overflow:visible;
        clear:both;
        font-family: Verdana, Geneva, Tahoma, sans-serif;
       }
       
       .left
       {
        float:left;
        height:430px;
        width:46%;
        margin-left:20px;
        margin-right:15px;
        padding:10px;
        opacity:0.5;
        border-radius:60px 60px 60px 60px;
        box-sizing: border-box;
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        font-style: italic;
       }
       
       .right
       {
        float:left;
        width:50%;
        height:420px;
        opacity:0.6;
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        font-style: italic;
       }
       .box
       {
        float:left;
        width:46%;
        min-height:200px;
        margin:2%;
        padding:15px;
        opacity:1;
        box-sizing: border-box;
        background-color:purple;
        border-radius:65px 65px 65px 65px;
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        font-style: italic;
       }
       p{
        height:0px;
        color:white;
        font-size:145%;
       }
       #p1
       {
        font-size:145%;
       }
       table
       {
        height:0px;
        text-align: center;
        
       }
      tr{
        height:0px;
        text-align: center;
      }

    #table1
{    width:100%;
    border-collapse:collapse;
    margin:auto;
    height:100%;
    font-family: 'Courier New', Courier, monospace;
    padding-bottom:0px;
    padding-top: 0px;
    padding-right: 0px;
    padding-left: 0px;
    }
    #td1
    {
    font-size:65px;
    text-align:middle;
    vertical-align:middle;
    color:white;
    font-family:'Poppins','Pacifico','Times New Roman';
    padding-bottom:0px;
    padding-top: 0px;
    padding-right: 0px;
    padding-left: 0px;
     }
     #td2{
    font-size:51px;
    text-align:middle;
    vertical-align:middle;
    color:white;
    font-family:'Times New Roman', Times, serif;
    padding-bottom:0px;
    padding-top: 0px;
    padding-right: 0px;
    padding-left: 0px;
     }
     #td3{
        font-size:33px;
        font-style: italic;
        text-align:middle;
        vertical-align:middle;
        color:white;
        font-family:'Times New Roman', Times, serif;
        padding-bottom:0px;
        padding-top: 0px;
        padding-right: 0px;
        padding-left: 0px;
     }
     #td4{
        font-size:20px;
    text-align:middle;
    vertical-align:middle;
    color:white;
    font-family:cursive;
    padding-bottom:0px;
    padding-top: 0px;
    padding-right: 0px;
    padding-left: 0px;
     }
     #td5{

    font-size:18px;
    text-align:middle;
    vertical-align:middle;
    color:white;
    
    font-family:cursive;
    padding-bottom:0px;
    padding-top: 0px;
    padding-right: 0px;
    padding-left: 0px;
     }
     #td6{
        padding:0px;
     }
     #td7{padding:0px;}
    .left1
       {
       float:left;
        height:212px;
        width:98%;
        margin-top:10px;
        padding:10px;
        opacity:0.8;
        border-radius:60px 60px 60px 60px;
        font-family: Verdana, Geneva, Tahoma, sans-serif;
       }
       .locationdiv
{
        height:75px;
        width:96%;
        opacity:0.8;
        padding-left:35px;
        margin-left:2%;
        margin-right:2%;
        border-radius:20px 25px 25px 25px;
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        font-style: italic;
}
      .left,.box,.left1,.locationdiv
      { 
    background: radial-gradient(circle at center, #051943 0%, #050a1b 100%);
   
    border-radius: 35px;
    border: 1px solid  #188887;
    box-shadow:  0 1px 1px    #188887;
    border:1px solid    #188887; /* teal neon */
    box-shadow:
    0 0 5px   #188887,
    0 0 10px    #188887,
    0 0 15px    #188887,
    inset 0 0 10px    #188887;
    transition: transform 0.3s ease,box-shadow 0.3s ease;
      }
 .left:hover,.box:hover,.left1:hover,.locationdiv:hover
 {
    transform: translateY(-10px) scale(1.02);
    box-shadow:0 0 5px #188887,0 0 10px   #188887;
 }


      b{
        color:#ffffff;
        font-weight:800;
      }
      
     .hourly-scroll {
    width: 100%;
    overflow-x:auto;
    -ms-overflow-style:none;
    scrollbar-width:none;   /* enables horizontal scroll */
}
 .hourly-scroll::-webkit-scrollbar
 {
    display:none;
 }
.hourly-scroll table {
    margin:0px 0px 0px 0px;
    padding:0px 0px 0px 0px;
    min-width:100px;   /* adjust to make scroll appear */
    border-collapse: collapse;
}
.hourly-scroll th, .hourly-scroll td {
    padding:5px;
    text-align: center;

}
#daytable
{  
    color:white;
    font-style: italic;
    font-size:19px;
    padding:1px 1px 1px 1px;
    font-family: Verdana, Geneva, Tahoma, sans-serif;
}
#hourtable
{
    margin:0px;
    padding:0px;
    color:white;
    font-size:19px;
}
#wimg
{
    width:85%;
    height:85%;
}
iframe
{
    width:95%;
    height:100%;
}


/* ==========================================================
   MOBILE DEVICES (max-width: 600px)
   ========================================================== */
@media (max-width: 600px) {

    body {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
    }

    .container {
        width: 100%;
        padding: 8px;
    }

    /* Stack all sections vertically */
    .left,
    .right,
    .box,
    .left1,
    .locationdiv {
        float: none;
        width: 100%;
        height: auto;
        margin: 10px 0;
        padding: 15px;
        border-radius: 25px;
    }

    /* Navbar spacing fix */
    a {
        display: inline-block;
        margin: 6px 6px;
        font-size: 16px;
    }

    i {
        font-size: 180%;
        display: block;
        margin-bottom: 10px;
    }

    /* Text scaling */
    p, #p1 {
        font-size: 18px;
        height: auto;
    }

    #td1 { font-size: 36px; }
    #td2 { font-size: 26px; }
    #td3 { font-size: 20px; }
    #td4 { font-size: 16px; }
    #td5 { font-size: 14px; }

    /* Images & map */
    #wimg {
        width: 100%;
        height: auto;
    }

    iframe {
        width: 100%;
        height: 280px;
        border-radius: 20px;
    }

    /* Forms */
    .locationdiv form {
        flex-direction: column;
        width: 100%;
        gap: 10px;
    }

    .locationdiv input,
    .locationdiv button {
        width: 100%;
    }

    /* Hourly scroll */
    .hourly-scroll table {
        min-width: 600px;
    }
}


/* ==========================================================
   TABLET DEVICES (601px - 992px)
   ========================================================== */
@media (min-width: 601px) and (max-width: 992px) {

    body {
        overflow-x: hidden;
    }

    .container {
        width: 100%;
        padding: 12px;
    }

    /* Stack main layout */
    .left,
    .right {
        width: 100%;
        float: none;
        height: auto;
        margin: 15px 0;
    }

    /* 2-column grid for boxes */
    .box {
        width: 48%;
        margin: 1%;
        min-height: 200px;
    }

    .left1,
    .locationdiv {
        width: 100%;
        height: auto;
        margin: 15px 0;
    }

    /* Typography */
    p, #p1 {
        font-size: 20px;
        height: auto;
    }

    #td1 { font-size: 48px; }
    #td2 { font-size: 34px; }
    #td3 { font-size: 26px; }
    #td4 { font-size: 18px; }
    #td5 { font-size: 16px; }

    i {
        font-size: 220%;
    }

    iframe {
        width: 100%;
        height: 380px;
        border-radius: 30px;
    }

    .hourly-scroll table {
        min-width: 750px;
    }
}


/* ==========================================================
   LARGE SCREENS (1200px and above)
   ========================================================== */
@media (min-width: 1200px) {

    .container {
        max-width: 1400px;
        margin: auto;
    }

    .left {
        width: 46%;
        height: 480px;
    }

    .right {
        width: 52%;
        height: 480px;
    }

    .box {
        width: 46%;
        min-height: 240px;
    }

    .left1 {
        height: 260px;
    }

    .locationdiv {
        height: 90px;
    }

    /* Better typography for big screens */
    p, #p1 {
        font-size: 22px;
    }

    #td1 { font-size: 72px; }
    #td2 { font-size: 56px; }
    #td3 { font-size: 38px; }
    #td4 { font-size: 22px; }
    #td5 { font-size: 20px; }

    iframe {
        height: 520px;
        border-radius: 50%;
    }
}




    </style>
    </head>
<body>
<audio autoplay hidden style="display:none;">
<source src="images/audio.mpeg" type="audio/mpeg">
</audio>

    <br>
 &emsp; <i style="color:#aa52c3; font-family:Pacifico; font-weight: bold;" >AuroraNova..</i>&emsp; &emsp;&emsp;&emsp;&emsp; &emsp;&emsp; &emsp;&emsp; &emsp;&emsp; &emsp;&emsp; &emsp;&emsp;&emsp;
      <a href="home.php" style="color:rgb(239, 225, 22)">Home</a> &emsp;
      <a href="privacy.html" style="color:rgb(239, 225, 22)">Privacy & Policy</a> &emsp;
      <a href="contactus.html" style="color:rgb(239, 225, 22)">Contact Us</a> &emsp;
      <a href="profile.php" style="color:rgb(239, 225, 22)">Profile</a>&emsp;
      <a href="my_msg.php" style="color:rgb(239, 225, 22)">Messages</a>
      
   <!-- class left madhe -->

<br>
<div class="container">
    <br>
    <div class="locationdiv" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
<!-- Search Form -->
<form action="home.php" method="POST" style="display:flex; gap:10px; align-items:center;">
    
    <h2 style="font-family:'Pacifico';">📍Enter Location:</h2><input type="text" name="loc" placeholder="Enter Location like (Pune,Mumbai,Tokyo,New York)" required
           style="padding:10px; width:325px; font-size:16px; border-radius:70px;">
    <input type="submit" value="Search"
           style="padding:10px 15px; font-size:16px; border-radius:70px; cursor:pointer;">
</form></i>

<!-- Generate Weather Report Form -->
<form action="send_weather.php" method="POST" style="display:flex; align-items:center;">
    <input type="hidden" name="loc" value="<?= htmlspecialchars($location) ?>">
    <button type="submit" style="padding:10px 20px; border-radius:70px; font-size:16px; cursor:pointer;">
        Generate Weather Report
    </button>
</form>
<!--Favourites Form-->
<form action="favorites_action.php" method="POST" style="display:flex;">
    <input type="hidden" name="city" value="<?= htmlspecialchars($location) ?>">
    <button type="submit" style="padding:8px 15px; border-radius:70px; cursor:pointer; background:#ffffff; color:black; border:none;">
        🩷Favorites
    </button>
</form>
</div>
<br><br>
   <div class="left">
   
    <table id="table1">
    <!-- ROW 1 -->
    <tr>
        <td id="td1"><!--temp    khali option ahe {} HTML CLICK KARUN  PHP SELECT KAR if not working-->
        &nbsp;<large style="color:rgb(224, 224, 32);"><?php echo $temp;?>°C&nbsp;</large></td>
        <td id=td6 rowspan="2"><img id="wimg" src="<?= $iconFile ?>" alt="Weather Icon">
        </td>
    </tr>

    <!-- ROW 2 -->
    <tr>
        <td id="td2"><!--cloudy-->
            <small><?php echo $weatherCondition; ?></small><br><large style="color:#d358f5;"><?php echo htmlspecialchars($location); ?></large>
        </td>
    </tr>

    <!-- ROW 3 -->
    <tr>
        <td id="td3" style="color:rgb(70, 217, 105);"><!--Pune-->
            <?php if($state) echo " ".htmlspecialchars($state); ?><?php if($country) echo ", ".htmlspecialchars($country); ?>
        </td>
        <td id=td7 rowspan="3"><!--map-->
          <iframe
        width="100%"
        height="100%"
        frameborder="0" style="border-radius:50%"
        src="https://www.google.com/maps?q=<?php echo $lat; ?>,<?php echo $lon; ?>&output=embed">
    </iframe>
        </td><!--ithe tu img tag add kar ani border radius full circle kar ,id=map  then #map madhi border radius circle kar-->
    </tr>

    <!-- ROW 4 -->
    <tr>

        <td id="td4"  style="color:rgb(239, 225, 22)"><!--Date-->
               🗓️ <?php echo $date; ?>    
            
        </td>
    </tr>

    <!-- ROW 5 -->
    <tr>
        <td id="td5"><!--Day,Time--> 
    
         🕑<?php echo $day; ?>,<?php echo $time; ?>
        </td>
    </tr>
</table>

  </div>

<div class="right">

<div class="box">
<table cellspacing="0px" cellpadding="0px">
<tr><center><img src="images/humidity.png" width="90px" height="90px"></center></tr>
<tr><center><p id="p1" ><b style="color:#d358f5;">Humidity</b></p></center></tr>
<tr><center> <!--ithe humidity data fetch karaycha-->
    <p><?php echo $humidity;   ?> %</p>
</center></tr>
</table>
</div>

<div class="box">
<table cellspacing="0px" cellpadding="0px">
<tr><center><img src="images/uvindex.png"width="90px" height="90px"></center></tr>
<tr><center><p id="p1"><b style="color:#d358f5;">UV Index</b></p></center></tr>
<tr><center> <!--ithe uv index data fetch karaycha-->
<p><?php echo $uvDesc;   ?></p>
</center>  </tr>
</table>
</div>

<div class="box">
<table cellspacing="0px" cellpadding="0px">
<tr><center><img src="images/wind.png" width="90px" height="90px"></center></tr>
<tr><center><p id="p1"><b style="color:#d358f5;">Wind Speed</b></p></center></tr>
<tr><center> <!--ithe wind speed data fetch karaycha-->
       <p><?php echo $windSpeed;   ?> m/s</p>
</center></tr>
</table>
</div>

<div class="box">
<table cellspacing="0px" cellpadding="0px">
<tr><center><img src= "images/pressure.png" width="90px" height="90px"></center></tr>
<tr><center><p id="p1"><b style="color:#d358f5;">Pressure</b></p></center></tr>
<tr><center> <!--ithe pressure data fetch karaycha-->
    <p><?php echo $pressure;   ?> hPa</p>
</center></tr>
</table>
</div>
</div>







<div class="container">
    <br><br>
<div class="right">

<div class="box">
<table cellspacing="0px" cellpadding="0px">
<tr><center><img src="images/sunrise.png" width="90px" height="90px"></center></tr>
<tr><center><p id="p1"><b style="color:#d358f5;">Sunrise</b></p></center></tr>
<tr><center> <!--ithe sunrise data fetch karaycha--> 
    <p><?php echo $sunrise;   ?> AM</p>
</center>  </tr>
</table>
</div>

<div class="box">
<table cellspacing="0px" cellpadding="0px">
<tr><center><img src="images/sunset.png" width="90px" height="90px"></center></tr>
<tr><center><p id="p1"><b style="color:#d358f5;">Sunset</b></p></center></tr>
<tr><center> <!--ithe sunset data fetch karaycha-->
        <p><?php echo $sunset;   ?>PM</p>
 </center>  </tr>
</table>
</div>



<div class="left1">
    <center><p id="p1" style="color:rgb(239, 225, 22); font-weight: bolder; font-family:cursive;">Hourly Forecast</p></center>
<br>
<!--ithe hourly forecast logic ahe-->
<div class="hourly-scroll">
    <table id="hourtable" border="0" cellspacing="0" cellpadding="0">
        <!-- Row 1: Time -->
        <tr>
            <th style="color:rgb(70, 217, 105);">Time</th>
            <?php foreach($hourlyForecast as $hour): 
                $hourTime = gmdate('h:i', $hour['dt'] + $timezone_offset); ?>
                <td><?php echo $hourTime; ?></td>
            <?php endforeach; ?>
        </tr>
        
        <!-- Row 2: Icon -->
        <tr>
    <th style="color:rgb(70, 217, 105);">Icon</th>
    <?php foreach($hourlyForecast as $hour): 
        $hourIcon = $hour['weather'][0]['icon'];
        $hourIconFile = $iconMap[$hourIcon] ?? 'images/weather/snow.png';
    ?>
        <td><img src="<?= $hourIconFile ?>" width="20px" height="20px"></td>
    <?php endforeach; ?>
</tr>

        
        <!-- Row 3: Temp -->
        <tr>
            <th style="color:rgb(70, 217, 105);">Temp</th>
            <?php foreach($hourlyForecast as $hour): 
                $hourTemp = $hour['main']['temp']; ?>
                <td><?php echo $hourTemp; ?>°C</td>
            <?php endforeach; ?>
        </tr>
    </table>
</div>
</div>

</div>

    </div>
</div>

<div class="left">
    <br>
    <center><p id="p1"><b style="color:rgb(239, 225, 22);">6 Days Weather Forecast</b></p></center>
    <!--ithe 7 days forecast ahe-->
    <br><br>
        <table id=daytable border="0" cellspacing="0" cellpadding="2" width="100%" >
    <tr>
        <th style="color:rgb(70, 217, 105);">Date</th>
        <th style="color:rgb(70, 217, 105);">Day</th>
        <th style="color:rgb(70, 217, 105);">Icon</th>
        <th style="color:rgb(70, 217, 105);">Min/Max Temp</th>
    </tr>
    <?php
foreach($dailyForecast as $day) {
    $forecastDate = gmdate('d-m-Y', $day['dt'] + $timezone_offset);
    $forecastDay  = gmdate('l', $day['dt'] + $timezone_offset); 
    $dayIconFile  = $iconMap[$day['icon']] ?? 'images/weather/snow.png';
    $tempMin = $day['temp_min'];
    $tempMax = $day['temp_max'];

    echo "<tr>
            <td>{$forecastDate}</td>
            <td>{$forecastDay}</td>
            <td><img src='{$dayIconFile}' width='40px' height='40px'></td>
            <td>{$tempMin}°C/{$tempMax}°C</td>
          </tr>";
}
?>

</table>
  </div>
</div>
<div class="container">
    <br><br>
    <div class="left">
        <iframe src="temp_graph.php?loc=<?=htmlspecialchars($location) ?>" style="width:100%; height:450px; border:none;"></iframe>
</div>
<div class="right">
<div class="box">
<table cellspacing="0px" cellpadding="0px">
<tr><center><img id="AQIMG" src="images/weather/AQI.png" width="130px" height="93px"></center></tr>
<tr><center><p id="p1"><b style="color:#d358f5;">AQI</b></p></center></tr>
<tr><center> <!--ithe sunrise data fetch karaycha--> 
    <p><?php echo $aqiDesc;   ?></p>
</center>  </tr>
</table>
</div>

<div class="box">
<table cellspacing="0px" cellpadding="0px">
<tr><center><img src="images/weather/dew.png" width="90px" height="90px"></center></tr>
<tr><center><p id="p1"><b style="color:#d358f5;">Dew Point</b></p></center></tr>
<tr><center> <!--ithe sunset data fetch karaycha-->
        <p><?php echo $dewPoint;   ?> °C</p>
 </center>  </tr>
</table>
</div>
<div class="left1">
           

<iframe src="line_graph.php?loc=<?=htmlspecialchars($location) ?>" style="width:100%; height:200px; border:none; overflow:hidden;"></iframe>

</div>

</div>
<br>
</div>


<a href="ai.html" class="chat-button">
    🤖
</a>

<style>
.chat-button{
    position: fixed;
    bottom: 25px;
    right: 25px;
    width: 70px;
    height: 70px;
    background: radial-gradient(circle,#aa52c3,#3b0a52);
    color: white;
    font-size: 32px;
    border-radius: 50%;
    text-align: center;
    line-height: 70px;
    text-decoration: none;
    box-shadow: 0 0 20px #aa52c3;
    animation: pulse 2s infinite;
    z-index: 999;
}
@keyframes pulse{
    0%{box-shadow:0 0 10px #aa52c3;}
    50%{box-shadow:0 0 25px #aa52c3;}
    100%{box-shadow:0 0 10px #aa52c3;}
}
</style>

</body>
</html>

