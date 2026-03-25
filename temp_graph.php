<?php  
$apiKey = "04c42c4ff9da1fc464dfd08f6f8e0742";  
if (!empty($_POST['loc'])) {
    $location = urlencode($_POST['loc']);
} elseif (!empty($_GET['loc'])) {
    $location = urlencode($_GET['loc']);
} else {
    $location = 'Pune';
}
function fetchData($url) {  
    $ch = curl_init($url);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
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
$forecastUrl = "https://api.openweathermap.org/data/2.5/forecast?q={$location}&appid={$apiKey}&units=metric";  
$forecastData = json_decode(fetchData($forecastUrl), true);  

$hourlyForecast = $forecastData['list'] ?? [];  
$timezone_offset = $forecastData['city']['timezone'] ?? 0;  

$chartHours = [];  
$chartTemps = [];  

foreach($hourlyForecast as $hour){  
    $chartHours[] = gmdate('H:i', $hour['dt'] + $timezone_offset);  
    $chartTemps[] = $hour['main']['temp'];  
}  

$maxTemp = max($chartTemps);  
$minTemp = min($chartTemps);  
$range = $maxTemp - $minTemp ?: 1;  

$numBars = count($chartTemps);  
$barWidth = min(50, floor(800 / $numBars)); // dynamic width max 50px
$gap =20; // space between bars
$chartHeight=400;
?>  

<!DOCTYPE html>  
<html lang="en">  
<head>  
<meta charset="UTF-8">  
<meta name="viewport" content="width=device-width, initial-scale=1.0">  
<title>Hourly Temp Chart</title>  
<style>  
html,body
{height:100%; margin:0; overflow-y:hidden;}
  body { background-color:  radial-gradient(circle at center, #051943 0%, #050a1b 100%);; color: white; font-family: Arial, sans-serif;}  

  .chart-container { width: 100%; padding: 20px 0; overflow-x:auto;  }  
  .scroll-hide{
    scrollbar-width:none; 
    -ms-overflow-style:none;
  }
  .scroll-hide::-webkit-scrollbar
  {display:none;}
  .chart {  
    display: flex;  
    align-items: flex-end;  
    gap: <?php echo $gap; ?>px;  
    height: 285px;  
    min-width: <?php echo ($barWidth + $gap) * $numBars; ?>px;  
    border-left: 2px solid #fff;  
    border-bottom: 2px solid #fff;  
    padding-bottom: 20px;
    position: relative;
    overflow:hidden;
  }  
  .bar {  
    width: <?php echo $barWidth; ?>px;  
    background-color:white;  
    text-align: center;  
    position: relative;  
    border-radius: 5px 5px 0 0;  
  }  
  .bar span {  
    position: absolute;  
    top: -20px;  
    width: 100%;  
    font-size: 12px;  
  }  
  .x-labels {  
    display: flex;  
    gap: <?php echo $gap; ?>px;  
    min-width: <?php echo ($barWidth + $gap) * $numBars; ?>px;  
    justify-content: flex-start;  
    margin-top: 5px;  
    font-size: 12px;  
    
  }  
  .x-labels div {  
    width: <?php echo $barWidth; ?>px;  
    text-align: center;  
    
  }  
  
</style>  
</head>  
<body>  

<h2><center style="color:rgb(239, 225, 22);">Hourly Temperature Bar Graph - <?php echo htmlspecialchars($location); ?></center></h2>  

<div class="chart-container scroll-hide">
  <div class="chart">  
  <?php  
  foreach($chartTemps as $i => $temp){  
      $barHeight = (($temp - $minTemp)/$range) * ($chartHeight-50); // scale to 250px max  
      echo '<div class="bar" style="height: '. $barHeight .'px;"><span>'.$temp.'°C</span></div>';  
  }  
  ?>  

</div>
  <div class="x-labels">  
  <?php  
  foreach($chartHours as $hour){  
      echo '<div>'.$hour.'</div>';  
  }  
  ?>  
  </div>
</div>

</body>  
</html>