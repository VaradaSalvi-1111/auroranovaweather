<?php
$apiKey = "04c42c4ff9da1fc464dfd08f6f8e0742";
$location = $_POST['loc'] ?? $_GET['loc'] ?? 'Pune';
$location = urlencode($location);

$url = "https://api.openweathermap.org/data/2.5/forecast?q={$location}&appid={$apiKey}&units=metric";
$data = file_get_contents($url);
$forecastData = json_decode($data, true);

$hourlyForecast = array_slice($forecastData['list'], 0, 12);

$times = [];
$temps = [];
$labels = [];

foreach ($hourlyForecast as $h) {
    $times[]  = strtotime($h['dt_txt']);
    $temps[]  = $h['main']['temp'];
    $labels[] = date('H:i', strtotime($h['dt_txt']));
}

$minTime = min($times);
$maxTime = max($times);
$timeRange = $maxTime - $minTime ?: 1;

$minTemp = min($temps);
$maxTemp = max($temps);
$tempRange = $maxTemp - $minTemp ?: 1;

$chartWidth  = max(610, ($maxTime - $minTime) / $timeRange * 610 + 50); 
$chartHeight = 95;
$padding     = 10;
?>

<!DOCTYPE html>
<html>
<head>
<style>
body{
    background: radial-gradient(circle at center, #051943 0%, #050a1b 100%);
    color:white;
    font-family:Arial;
    overflow-y:hidden;
    
}
.chart-container
{
     overflow-x:auto; 
    
}
.scroll-hide{
    scrollbar-width:none; 
    -ms-overflow-style:none;
  }
  .scroll-hide::-webkit-scrollbar
  {display:none;}
.chart{
    position:relative;
    width:<?= $chartWidth ?>px;
    height:<?= $chartHeight ?>px;
    border-left:1px solid white;
    border-bottom:1px solid white;
     overflow:hidden;
}
.point{
    position:absolute;
    width:6px;
    height:6px;
    background:red;
    border-radius:50%;
    transform:translate(-50%,-50%);
}

.line{
    position:absolute;
    height:3px;
    background:red;
    transform-origin:left center;
}

.x-labels{
    width:<?= $chartWidth ?>px;
    display:flex;
    justify-content:space-between;
    font-size:12px;
    margin-top:4px;
}
</style>
</head>

<body>
    <h3 align="center" style="color:rgb(239, 225, 22);">Hourly Temperature (<?= htmlspecialchars($location) ?>)</h3>

<div class="chart-container scroll-hide">

<div class="chart">
<?php
$prevX = $prevY = null;

foreach ($temps as $i => $temp) {
    $x = (($times[$i] - $minTime) / $timeRange) * $chartWidth;
    $y = ($chartHeight - $padding) - (($temp - $minTemp) / $tempRange) * ($chartHeight - $padding);

    echo "<div class='point' style='left:{$x}px; top:{$y}px'></div>";

    if ($i > 0) {
        $dx = $x - $prevX;
        $dy = $y - $prevY;
        $length = sqrt($dx*$dx + $dy*$dy);
        $angle = rad2deg(atan2($dy, $dx));

        echo "<div class='line'
              style='left:{$prevX}px; top:{$prevY}px;
              width:{$length}px; transform:rotate({$angle}deg)'></div>";
    }

    $prevX = $x;
    $prevY = $y;
}
?>
</div>

<div class="x-labels">
<?php foreach ($labels as $l) echo "<div>$l</div>"; ?>
</div>
</div>
</body>
</html>