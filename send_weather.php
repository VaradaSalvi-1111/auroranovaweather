<?php
session_start();

// Make sure user is logged in
if (!isset($_SESSION['user'])) {
    die("<h2>Please login first to generate your report.</h2>");
}

// Get location from POST
$location = $_POST['loc'] ?? 'Pune';
$apiKey = "04c42c4ff9da1fc464dfd08f6f8e0742";
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
// Fetch weather data
$weatherData = json_decode(file_get_contents("https://api.openweathermap.org/data/2.5/weather?q={$location}&appid={$apiKey}&units=metric"), true);

$temp = $weatherData['main']['temp'] ?? '--';
$humidity = $weatherData['main']['humidity'] ?? '--';
$wind = $weatherData['wind']['speed'] ?? '--';
$condition = $weatherData['weather'][0]['description'] ?? '--';
$lat = $weatherData['coord']['lat'] ?? 0;
$lon = $weatherData['coord']['lon'] ?? 0;
$timezone_offset = $weatherData['timezone'] ?? 0;
$state = $weatherData['name'] ?? '';
$country = $weatherData['sys']['country'] ?? '';
$date = gmdate('d-m-Y', time() + $timezone_offset);
$time = gmdate('h:i:s A', time() + $timezone_offset);
if ($temp !== '--' && $humidity !== '--') {
    $dewPoint = round($temp - ((100 - $humidity) / 5), 1);
} else {
    $dewPoint = '--';
}

$pressure = $weatherData['main']['pressure'] ?? '--';
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🌤 AuroraNova Weather Report</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background:url('images/bg2.jpeg');
             background-repeat: no-repeat;
            background-size: 100% 100%;
            background-attachment: fixed;
            background-position: center;
            color: #fff;
            margin: 0;
            padding: 0;
        }
        .report-container {
            max-width: 700px;
            margin: 50px auto;
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.4);
            backdrop-filter: blur(8px);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2em;
            color: #304082ff;
            background-color:white;
        }
        .location {
            text-align: center;
            font-size: 1.2em;
            margin:auto;
        }
        ul {
            list-style: none;
            padding: 0;
    
        }
        li {
            background: rgba(255,255,255,0.2);
            margin: 8px 0;
            padding: 12px 15px;
            border-radius: 12px;
            font-size: 1.1em;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        li span {
            font-weight: bold;
            color:  #304082ff;
        }
        .footer {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9em;
            color: #ccc;
        }
        button.print-btn {
            display: block;
            margin: 20px auto 0;
            padding: 10px 25px;
            font-size: 1em;
            background: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            color: #1e3c72;
            font-weight: bold;
        }
        button.print-btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="report-container">
        <h1 style="background-color:white; border-radius:60px; width:550px; margin:auto;">🌤 AuroraNova Weather Report</h1>
        <br>
        <div class="location" style="color:#304082ff; background-color:white; border-radius:60px; width:300px">
            <?php echo htmlspecialchars($location) . ", " . htmlspecialchars($state) . ", " . htmlspecialchars($country); ?><br>
            <?php echo $date . " | " . $time; ?>
        </div>
        <ul>
            <h3><li style="color:#304082ff; background-color:white;">Temperature <span><?php echo $temp; ?> °C</span></li></h3>
            <h3><li style="color:#304082ff;  background-color:white;">Humidity <span><?php echo $humidity; ?> %</span></li></h3>
            <h3><li style="color:#304082ff;  background-color:white;">Wind Speed <span><?php echo $wind; ?> m/s</span></li></h3>
            <h3><li style="color:#304082ff;  background-color:white;">Condition <span><?php echo ucfirst($condition); ?></span></li></h3>
            <h3><li style="color:#304082ff;  background-color:white;">UV Index <span><?php echo $uvDesc;   ?></span></li></h3>
            <h3><li style="color:#304082ff;  background-color:white;">Pressure <span><?php echo $pressure;   ?> hPa </span></li></h3>
            <h3><li style="color:#304082ff;  background-color:white;">Sunrise <span><?php echo $sunrise;   ?> AM</span></li></h3>
            <h3><li style="color:#304082ff;  background-color:white;">Sunset <span><?php echo $sunset;   ?> PM</span></li></h3>
            <h3><li style="color:#304082ff;  background-color:white;">Air Quality Index <span><?php echo $aqiDesc;   ?> </span></li></h3>
            <h3><li style="color:#304082ff;  background-color:white;">Dew Point <span><?php echo $dewPoint;   ?> °C</span></li></h3>
        </ul>
        <button class="print-btn" onclick="window.print()">🖨 Print Report</button>
        <div class="footer" style="color:white;">This report is generated automatically by AuroraNova</div>
    </div>
</body>
</html>