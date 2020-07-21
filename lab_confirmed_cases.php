<?php

$Title = "Lab Confirmed New Cases | Wigan COVID-Tracker";
$PageDescription = "Shows confirmed COVID-19 Lab Results reported for individuals living in Wigan; includes a comparison 
    of the Cumulative Rate of Infection for COVID-19 between Wigan, the North West and England.";
$PageKeywords = "lab confirmed cases, England, North West, wigan, COVID-19, coronavirus, lockdown";
$LastUpdated = "1st June 2020";

$configs = include('config.php');
include('db.php');

$db = new db($configs['servername'], $configs['username'], $configs['password'], $configs['database']);

$myQuery = "SELECT `confirmed_labs`.`date`, `confirmed_labs`.`england_labs`, `confirmed_labs`.`england_cumm`, 
    `confirmed_labs`.`nw_labs`, `confirmed_labs`.`nw_cumm`, 
    `confirmed_labs`.`wigan_labs`, `confirmed_labs`.`wigan_cumm`
FROM `confirmed_labs`
ORDER BY `confirmed_labs`.`date` DESC LIMIT 30";

$fields = $db->query($myQuery)->fetchAll();

$db->close();

$values = [];
foreach ($fields as $field) {   
    array_push($values, array("date" => $field['date'], "england_labs" => $field['england_labs'], "england_c" => $field['england_cumm'], 
    "nw_labs" => $field['nw_labs'], "nw_c" => $field['nw_cumm'], 
    "wigan_labs" => $field['wigan_labs'], "wigan_c" => $field['wigan_cumm']
    ));
}

$countArrayLength = count($values);

include('header.php');
?>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        // Load Charts and the corechart package.
        google.charts.load('current', {packages: ['corechart', 'line']});

        // Draw a line chart for all of the Regions together
        google.charts.setOnLoadCallback(drawWiganLabs);

        // Draw a line chart for cummulative Rate
        google.charts.setOnLoadCallback(drawCummulativeRate);


        // Callback that draws the line for all of the Regions together
        function drawWiganLabs() {
            var data = new google.visualization.DataTable();

            data.addColumn('date', 'Date');
            data.addColumn('number', 'Confirmed Labs - Wigan');

            data.addRows([
<?php

for ($i =($countArrayLength - 30); $i < $countArrayLength; $i++) {
    list($year, $month, $day) = explode('-',$values[$i]['date']);
      echo "[new Date(".$year.", ".($month - 1).", ".$day."), ".$values[$i]['wigan_labs']."]";

    if ($i != ($countArrayLength - 1)){
        echo ", \n";
    }
    else {
        echo "]);\n";
    }
}

?>
    // Set Options for all of the Regions together
      var options = {
        title: 'Confirmed COVID-19 Lab Results - Wigan (last 30 days)',
        width:1200,
        height:500
      };

    // Instantiate and draw the chart for all the Regions together
      var chart = new google.visualization.ColumnChart(document.getElementById('WiganLabs_div'));
      chart.draw(data, options);
    }

//=============================================

    // Callback that draws the line chart for 100k data
    function drawCummulativeRate(){
        var data = new google.visualization.DataTable();
        // Create the data table for 100k data
        data.addColumn('date', 'Date');
        data.addColumn('number', 'England');
        data.addColumn('number', 'North West');
        data.addColumn('number', 'Wigan');



        data.addRows([
<?php

for ($i = 0; $i < $countArrayLength; $i++) {
    list($year, $month, $day) = explode('-',$values[$i]['date']);


    echo "[new Date(".$year.", ".($month - 1).", ".$day."), ". $values[$i]['england_c'] .", "
        . $values[$i]['nw_c'].", "
        . $values[$i]['wigan_c']."]";

    if ($i != ($countArrayLength - 1)){
        echo ", \n";
    }
    else {
        echo "]);\n";
    }
}

?>

    // set options for North West Data
    var options = {
        title: 'Cummulative Rate of Infection per 100k (last 30 days)',
        width:1200,
        height:500,
      };

    // Instantiate and draw the chart for North West Data Only
     var chart = new google.visualization.ComboChart(document.getElementById('Cummuliative_Rate_div'));
     chart.draw(data, options);
  
    }
//=============================================


    </script>
    <style>
.chartWithOverlay {
           position: relative;
           width: 1200px;
    }

.overlay {
    //width: 200px;
    width: 230px;
    height: 320px;

    position: absolute;
    top: 60px;  /* chartArea top  */
    left: 765px; /* chartArea left */
    margin: 30px;
    background-color: #808080;
    border: 1px solid black;
    opacity: 0.6;
}


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
        Wigan cumulative rate of infection per 100k remains much higher than the North West 
        and the whole of England.  While not many new cases from Wigan has been reported, 
        I’m not clear as to whether or not that’s because reporting from Wigan has stopped.
    </p>

</div>

    <p>&nbsp;</p>

    <h3>Confirmed COVID-19 Lab Results - Wigan<sup><a href="#FootNote1">1</a></sup></h3>

    <div class="chartWithOverlay">

        <div id="WiganLabs_div"></div>
        <noscript>Your browser does not support JavaScript!</noscript>

        <div class="overlay">
        <div style="font-family:'Arial Black'; font-size: 20px; writing-mode: vertical-rl; text-orientation: mixed;">Provisional Data</div>
        </div>

    </div>


    <h3>Cummulative Rate of Infection per 100k<sup><a href="#FootNote1">1</a></sup></h3>
        <div id="Cummuliative_Rate_div"></div>
        <noscript>Your browser does not support JavaScript!</noscript>
    

    <p>&nbsp;</p>

    <hr />
    <h3>Footnotes</h3>
        <ol>
            <li id="FootNote1">Data collected from <a href="https://coronavirus.data.gov.uk/" target="_blank">
                Gov.UK's Coronavirus Dashboard</a>. The most recent 5 days will be provisional data as some trusts take a few days to report up their numbers.
            </li>

        </ol>
</div>

<?php include('footer.php'); ?>