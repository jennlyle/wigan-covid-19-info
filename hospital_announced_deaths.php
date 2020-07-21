<?php
$constants = include('constants.php');

$configs = include('config.php');
include('db.php');
$db = new db($configs['servername'], $configs['username'], $configs['password'], $configs['database']);

$myQuery = "SELECT `hospital_announced_deaths`.`date`, `hospital_announced_deaths`.`england`, `hospital_announced_deaths`.`north_west`, `hospital_announced_deaths`.`wigan`, `hospital_announced_deaths`.`manchester`
FROM `hospital_announced_deaths` ORDER BY `hospital_announced_deaths`.`date` DESC LIMIT 30";

$fields = $db->query($myQuery)->fetchAll();

$count = 0;
foreach ($fields as $field) {   
    $myArray[$count] = array("date" => $field['date'], "england" => $field['england'], "north_west" => $field['north_west'], 
    "wigan" => $field['wigan'], "manchester" => $field['manchester']);
    $count++;
}
unset($fields);
unset($field);

$db->close();

$Title = "Hospital Announced Deaths | Wigan COVID-Tracker";
$PageDescription = "Includes a comparison of Announced Hospital Deaths between WWL and Manchester University NHS Foundation Trusts; 
    and includes a comparison of Announced Deaths from COVID-19 between Wigan, North West, and England per 100k population.";
$PageKeywords = "hospital announced deaths, England, North West, wigan, COVID-19, coronavirus, lockdown";
$LastUpdated = "1st June 2020";

include('header.php');
?>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {packages: ['corechart', 'line']});
        // Draw line chart for comparing hospitals
        google.charts.setOnLoadCallback(drawCompareHospitals);
        // Draw a combo chart for comparing hosptial deaths per 100k
        google.charts.setOnLoadCallback(drawCompareHospitals100k);
        function drawCompareHospitals() {
            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date');
            data.addColumn('number', 'Wrightington, Wigan & Leigh NHS Foundation Trust');
            data.addColumn('number', 'Manchester University NHS Foundation Trust');
            data.addRows([
<?php
for ($i = 0; $i < (count($myArray) - 1); $i++) {
    list($year, $month, $day) = explode('-',$myArray[$i]['date']);
    echo "[new Date(".$year.", ".($month - 1).", ".$day."), "
        .$myArray[$i]['wigan'].","
        .$myArray[$i]['manchester']."]";
    if ($i != (count($myArray) - 2)){
        echo ", \n";
    }
    else {
        echo "]);\n";
    }
}
?>
    // Set chart options
            var options = {
                title: 'Hospital Announced Deaths (last 30 days)',
                width:1200,
                height:500
            };
    // Instantiate and draw the chart
            var chart = new google.visualization.LineChart(document.getElementById('AllRegions_div'));
            chart.draw(data, options);
        }
//=============================================
        function drawCompareHospitals100k(){
            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date');
            data.addColumn('number', 'England');
            data.addColumn('number', 'North West');
            data.addColumn('number', 'Wigan');
            data.addRows([
<?php
for ($i = 0; $i < (count($myArray) - 1); $i++) {
    list($year, $month, $day) = explode('-',$myArray[$i]['date']);

    echo "[new Date(".$year.", ".($month - 1).", ".$day."), "
        . $myArray[$i]['england'] / ($constants['england_population'] / 100000) .", "
        . $myArray[$i]['north_west'] / ($constants['nw_population'] / 100000).", "
        . $myArray[$i]['wigan'] / ($constants['wigan_population'] / 100000)."]";

    if ($i != (count($myArray) - 2)){
        echo ", \n";
    }
    else {
        echo "]);\n";
    }
}
?>
    // Set chart options
            var options = {
                title: 'Announced Hospital Deaths per 100k',
                width:1200,
                height:500,
                seriesType: 'bars',
                series: {
                    3: {type: 'line', color: 'green'},
                },
            };
    // Instantiate and draw the chart
            var chart = new google.visualization.ComboChart(document.getElementById('100k_div'));
            chart.draw(data, options); 
        }
//=============================================
    </script>
    <style>
    .chartWithOverlay {
        position: relative;
        width: 1200px;
        }
    .overlay_1 {
        //width: 200px;
        width: 210px;
        height: 320px;
        position: absolute;
        top: 60px;  /* chartArea top  */
        left: 785px; /* chartArea left */
        margin: 30px;
        background-color: #808080;
        border: 1px solid black;
        opacity: 0.6;
    }
    .overlay_2 {
        //width: 200px;
        width: 225px;
        height: 320px;
        position: absolute;
        top: 100px;  /* chartArea top  */
        left: 770px; /* chartArea left */
        margin: 30px;
        background-color: #808080;
        border: 1px solid black;
        opacity: 0.6;
    }
    </style>

    </head>
    <body>
    <div class="container">
        <div class="container">
            <header><h1><?php echo $Title; ?></h1></header>
<?php include('navigation.php'); ?>
            <div class="container">
                <p>&nbsp;</p>
                <h2>Comments</h2>
                <p>
                Announced deaths in Wigan has stabilised for the time being.
                </p>
            </div>
            <p>&nbsp;</p>
            <h3>Comparing WWL with Manchester University Trust<sup><a href="#FootNote1">1</a></sup></h3>
            <div class="chartWithOverlay">
                <div id="AllRegions_div"></div>
                <noscript>Your browser does not support JavaScript!</noscript>
                <div class="overlay_1">
                <div style="font-family:'Arial Black'; font-size: 20px; writing-mode: vertical-rl; text-orientation: mixed;">Provisional Data</div>
                </div>
            </div>
            <div class="chartWithOverlay">
                <h3>Hospital Deaths per 100k<sup><a href="#FootNote1">1</a></sup></h3>
                <div id="100k_div"></div>
                <noscript>Your browser does not support JavaScript!</noscript>

                <div class="overlay_2">
                    <div style="font-family:'Arial Black'; font-size: 20px; writing-mode: vertical-rl; text-orientation: mixed;">Provisional Data</div>
                </div>
            </div>
            <p>&nbsp;</p>
            <hr />
            <h3>Footnotes</h3>
            <ol>
                <li id="FootNote1">Data collected from <a href="https://www.england.nhs.uk/statistics/statistical-work-areas/covid-19-daily-deaths/" target="_blank">
                    NHS England's COVID-19 Daily Deaths</a>. The most recent 5 days will be provisional data as some trusts take a few days to report up their numbers.
                </li>
            </ol>
        </div>
<?php include('footer.php'); ?>