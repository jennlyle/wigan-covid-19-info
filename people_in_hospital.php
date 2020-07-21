<?php
$constants = include('constants.php');

$configs = include('config.php');
include('db.php');
$db = new db($configs['servername'], $configs['username'], $configs['password'], $configs['database']);

$myQuery = "SELECT `people_in_hospital`.`date`, 
  `people_in_hospital`.`east_of_england`, 
  `people_in_hospital`.`london`, 
  `people_in_hospital`.`midlands`, 
  `people_in_hospital`.`north_east`, 
  `people_in_hospital`.`north_west`, 
  `people_in_hospital`.`south_east`, 
  `people_in_hospital`.`south_west`, 
  `people_in_hospital`.`scotland`, 
  `people_in_hospital`.`wales`, 
  `people_in_hospital`.`north_ireland` 
  FROM `people_in_hospital` 
  ORDER BY `people_in_hospital`.`date` DESC LIMIT 30";

$fields = $db->query($myQuery)->fetchAll();

$count = 0;
foreach ($fields as $field) {   
    $myArray[$count] = array("date" => $field['date'], "east_of_england" => $field['east_of_england'], "london" => $field['london'], "midlands" => $field['midlands'], "north_east" => $field['north_east'], "north_west" => $field['north_west'], 
    "south_east" => $field['south_east'], "south_west" => $field['south_west'], "scotland" => $field['scotland'], 
    "wales" => $field['wales'], "north_ireland" => $field['north_ireland']);
    $count++;
}
unset($fields);
unset($field);

$db->close();

$Title = "People in Hospital in the North West | Wigan COVID-Tracker";
$PageDescription = "Includes a summary of total people in hospital for COVID-19, comparing English Regions and UK Nations; 
  an estimate of how close to hospital capacity North West hospitals are; and includes a chart comparting recent daily 
  Admissions and Discharges from North West hospitals for COVID-19.";
$PageKeywords = "People in Hospital, All of England, East of England, London, Midlands, North East, North West, South East, South West, Scotland, Wales, Northern Ireland, COVID-19, coronavirus, lockdown";

include('header.php');

?>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        // Load Charts and the corechart package.
        google.charts.load('current', {packages: ['corechart', 'line']});

        // Draw a line chart for all of the Regions together
        google.charts.setOnLoadCallback(drawRegionChart);

        // Draw a combo chart for just North West Data
        google.charts.setOnLoadCallback(drawNorthWestChart);

        // Draw a combo chart for North West Hospital Admissions and Discharges
        google.charts.setOnLoadCallback(drawNorthWestAdmitDischarge);

        // Callback that draws the line for all of the Regions together
        function drawRegionChart() {
            var data = new google.visualization.DataTable();

            data.addColumn('date', 'Date');
            data.addColumn('number', 'East of England');
            data.addColumn('number', 'London');
            data.addColumn('number', 'Midlands');
            data.addColumn('number', 'North East and Yorkshire');
            data.addColumn('number', 'North West');
            data.addColumn('number', 'South East');
            data.addColumn('number', 'South West');
            data.addColumn('number', 'Scotland');
            data.addColumn('number', 'Wales');
            data.addColumn('number', 'Northern Ireland');

            data.addRows([
<?php

for ($i = 0; $i < (count($myArray) - 1); $i++) {
    list($year, $month, $day) = explode('-',$myArray[$i]['date']);

    // Account for North_Ireland's Nulls at the end of the array
    if ($myArray[$i]['north_ireland'] === 0){
      $nIre = 'null';
    }
    else{
      $nIre = $myArray[$i]['north_ireland'];
    }


    echo "[new Date(".$year.", ".($month - 1).", ".$day."), "
      .$myArray[$i]['east_of_england'].",".$myArray[$i]['london'].",".$myArray[$i]['midlands'].","
      .$myArray[$i]['north_east'].",".$myArray[$i]['north_west'].",".$myArray[$i]['south_east'].",".$myArray[$i]['south_west'].","
      .$myArray[$i]['scotland'].",".$myArray[$i]['wales'].",".$nIre."]";
    if ($i != (count($myArray) - 2)){
        echo ", \n";
    }
    else {
        echo "]);\n";
    }
}

$EnglandSum = $myArray[0]['east_of_england'] + $myArray[0]['london'] + $myArray[0]['midlands'] + 
  $myArray[0]['north_east'] + $myArray[0]['north_west'] + $myArray[0]['south_east']
  + $myArray[0]['south_west'];

$UKSum = $EnglandSum + $myArray[0]['scotland'] + $myArray[0]['wales'] + $myArray[0]['north_ireland'];

$countArrayLength = count($myArray);

?>
    // Set Options for all of the Regions together
      var options = {
        title: 'People in Hospital with COVID-19 (last 30 days)',
        width:1200,
        height:500
      };

    // Instantiate and draw the chart for all the Regions together
      var chart = new google.visualization.LineChart(document.getElementById('AllRegions_div'));
      chart.draw(data, options);
    }

    // Callback that draws the line chart for North West data only
    function drawNorthWestChart(){
        var data = new google.visualization.DataTable();
        // Create the data table for North West data only
        data.addColumn('date', 'Date');
        data.addColumn('number', 'North West');

        data.addColumn('number', '7-Day Average');
        data.addColumn('number', '80% Capacity');
        data.addColumn('number', '100% Capacity');

        data.addRows([
<?php

for ($i = 0; $i < (count($myArray) - 1); $i++) {
    list($year, $month, $day) = explode('-',$myArray[$i]['date']);

    if ($i > (29 - 7)){
      $average = null;
    }
    else{
      $average = round($myArray[$i]['north_west'] + $myArray[$i + 1]['north_west'] + $myArray[$i + 2]['north_west'] + $myArray[$i + 3]['north_west'] + $myArray[$i + 4]['north_west'] + $myArray[$i + 5]['north_west'] + $myArray[$i + 6]['north_west']) / 7;
      
    }

    echo "[new Date(".$year.", ".($month - 1).", ".$day."), ".$myArray[$i]['north_west'].", ". $average. "," . 1055 .  "," . 1319 . "]";

    if ($i != (count($myArray) - 2)){
        echo ", \n";
    }
    else {
        echo "]);\n";
    }
}

?>
    // set options for North West Data
    var options = {
        title: 'North West - People in Hospital with COVID-19 (last 30 days)',
        width:1200,
        height:500,
        seriesType: 'bars', 
        series: {
          1: {type: 'line', color: 'orange'},
          2: {type: 'line', color: 'cyan'},
          3: {type: 'line', color: 'red'},
        },
      };

    // Instantiate and draw the chart for North West Data Only
     var chart = new google.visualization.ComboChart(document.getElementById('NWChart_div'));
     chart.draw(data, options);
  
    }

//================================================================================

    // Callback that draws the line chart for North West data only
    function drawNorthWestAdmitDischarge(){
        var data = new google.visualization.DataTable();
        // Create the data table for North West data only
        data.addColumn('date', 'Date');
        data.addColumn('number', 'North West');
        data.addRows([
<?php

for ($i = 0; $i < (count($myArray) - 1); $i++) {
    list($year, $month, $day) = explode('-',$myArray[$i]['date']);

    if ($i === 0){
      $num = 0;
    }
    else {
      $num = $myArray[$i]['north_west'] - $myArray[$i-1]['north_west'];
      $num = $num * -1;
    }   

    echo "[new Date(".$year.", ".($month - 1).", ".$day."), ".$num."]";

    if ($i != (count($myArray) - 2)){
        echo ", \n";
    }
    else {
        echo "]);\n";
    }
}

?>

    // set options for North West Data
    var options = {
        title: 'North West COVID-19 Daily Admissions vs Discharges (last 30 days)',
        width:1200,
        height:500,
        seriesType: 'bars', 
      };

    // Instantiate and draw the chart for North West Data Only
     var chart = new google.visualization.ComboChart(document.getElementById('NWAdmitDis_div'));
     chart.draw(data, options);
  
    }
//===============================================================================

    </script>
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
    The North West still has the most people currently in hospital for COVID-19 treatment.  However, 
    from a per capita standpoint, Midlands has now taken the lead what with 11 people per 100k 
    who is in hospital, right now, getting COVID-19 treatment.
    </p>

    <p>
    If the NHS were in full operation (currently it’s not as all non-urgent surgeries are still currently on 
    suspension), the North West’s capacity would currently be at 83%.  It’s usually at 82% when hospitals 
    would begin to have to turn people away.  
    </p>
</div>

    <p>&nbsp;</p>
      <h2>Summary<sup><a href="#FootNote1">1</a></sup></h2>
      <table class="table table-hover">
        <thead>
          <th scope="col" colspan="2">Location</th>
          <th scope="col"><?php echo $myArray[0]['date'];    ?></th>
          <th scope="col">Per 100k</th>
        </thead>
        <tbody>
          <tr>
            <th scope="row" colspan="2">UK</th>
            <th scope="row"><?php echo  number_format($UKSum);?></th>

            <td><?php echo number_format($UKSum / ($constants['ukPopulation'] / 100000), 2); ?></td>
          <tr>
          <tr>
            <th scope="row" colspan="2">England</th>
            <th scope="row"><?php echo  number_format($EnglandSum);?></th>
            <td><?php echo number_format($EnglandSum / ($constants['england_population'] / 100000), 2); ?></td>
          <tr>
          <tr>
          <td></td>
            <th scope="row">East of England</th>
            <td><?php echo  number_format($myArray[0]['east_of_england']);?></td>
            <td><?php echo number_format($myArray[0]['east_of_england'] / ($constants['east_of_england_population'] / 100000), 2); ?></td>
          <tr>
          <tr>
          <td></td>
            <th scope="row">London</th>
            <td><?php echo  number_format($myArray[0]['london']);?></td>
            <td><?php echo number_format($myArray[0]['london'] / ($constants['london_population'] / 100000), 2); ?></td>
          <tr>
          <tr>
          <td></td>
            <th scope="row">Midlands</th>
            <td><?php echo  number_format($myArray[0]['midlands']);?></td>
            <td><?php echo number_format($myArray[0]['midlands'] / ($constants['midlands_population'] / 100000), 2); ?></td>
          <tr>
          <tr>
          <td></td>
            <th scope="row">North East and Yorkshire</th>
            <td><?php echo  number_format($myArray[0]['north_east']);?></td>
            <td><?php echo number_format($myArray[0]['north_east'] / ($constants['ne_population'] / 100000), 2); ?></td>
          <tr>
          <tr>
          <td></td>
            <th scope="row">North West</th>
            <td><?php echo  number_format($myArray[0]['north_west']);?></td>
            <td><?php echo number_format($myArray[0]['north_west'] / ($constants['nw_population'] / 100000), 2); ?></td>
          <tr>
          <tr>
          <td></td>
            <th scope="row">South East</th>
            <td><?php echo  number_format($myArray[0]['south_east']);?></td>
            <td><?php echo number_format($myArray[0]['south_east'] / ($constants['se_population'] / 100000), 2); ?></td>
          <tr>
          <tr>
          <td></td>
            <th scope="row">South West</th>
            <td><?php echo  number_format($myArray[0]['south_west']);?></td>
            <td><?php echo number_format($myArray[0]['south_west'] / ($constants['sw_population'] / 100000), 2); ?></td>
          <tr>
          <tr>
            <th scope="row" colspan="2">Scotland</th>
            <th scope="row"><?php echo  number_format($myArray[0]['scotland']);?></th>
            <td><?php echo number_format($myArray[0]['scotland'] / ($constants['scotland_population'] / 100000), 2); ?></td>
          <tr>
          <tr>
            <th scope="row" colspan="2">Wales</th>
            <th scope="row"><?php echo  number_format($myArray[0]['wales']);?></th>
            <td><?php echo number_format($myArray[0]['wales'] / ($constants['wales_population'] / 100000), 2); ?></td>
          <tr>
          <tr>
            <th scope="row" colspan="2">Northern Ireland<sup><a href="#FootNote6">6</a></sup></th>
            <th scope="row"><?php echo  number_format($myArray[2]['north_ireland']);?></th>
            <td><?php echo number_format($myArray[2]['north_ireland'] / ($constants['nireland_population'] / 100000), 2); ?></td>
          <tr>

        </tbody>
      </table>

      <p>&nbsp;</p>

        <div id="AllRegions_div"></div>
    
        <h2>North West England - Estimated Capacity for COVID-19 Beds<sup><a href="#FootNote2">2</a></sup></h2>
        <table class="table table-hover">
            <thead>
            <th scope="col">Available Beds<sup><a href="#FootNote3">3</a></sup></th>
            <th scope="col">Occupied Beds<sup><a href="#FootNote4">4</a></sup></th>
            <th scope="col"># COVID Beds</th>
            <th scope="col">% Capacity<sup><a href="#FootNote5">5</a></sup></th>
            </thead>
            <tbody>
            <tr>
                <td><?php echo number_format($constants['available_beds_NW']); ?></th>
                <td><?php echo number_format($constants['occupied_beds_NW']); ?></th>
                <td><?php echo number_format($myArray[0]['north_west']); ?></td>
                <td><?php echo number_format(($constants['occupied_beds_NW'] + $myArray[0]['north_west']) / $constants['available_beds_NW'],2) * 100; ?>%</td>
          <tr>
        </table>

        <div id="NWChart_div"></div>
        <noscript>Your browser does not support JavaScript!</noscript>

        <h2>North West England - Daily Admissions vs Discharges<sup><a href="#FootNote1">1</a></sup></h2>

        <div id="NWAdmitDis_div"></div>
        <noscript>Your browser does not support JavaScript!</noscript>

    </div>

  <div class="container">
    <hr />
        <h3>Additional Data</h3>
        <p>&nbsp;</p>

        <h4>North West Recorded 'Nightingale' Hospitals and Wards</h4>
        <table class="table table-hover">
            <thead>
                <th scope="col">Name</th>
                <th scope="col">Beds Added</th>
                <th scope="col">Date Opened</th>
                <th scope="col">Source</th>
            </thead>
            <tbody>
                <tr>
                    <td>Royal Liverpool Hospital</td>
                    <td>65</th>
                    <td>5th May 2020</td>
                    <td><a href="https://www.constructionnews.co.uk/contractors/laing-orourke/royal-liverpool-hospital-to-partially-open-for-covid-19-care-01-05-2020/" target="_blank">Link</a></td>
                <tr>
                <tr>
                    <td>Stoke on Trent</td>
                    <td>ready to open within 48 hours as of... </th>
                    <td> 29 April 2020</td>
                    <td><a href="https://www.stokesentinel.co.uk/news/stoke-on-trent-news/three-north-staffordshire-hospitals-ready-4084530" target="_blank">Link</a></td>
                <tr>
                <tr>
                    <td>Wigan Bryn Ward</td>
                    <td>50</th>
                    <td>13th May 2020</td>
                    <td><a href="https://www.wigantoday.net/news/uk-news/wigan-infirmary-officially-opens-its-new-covid-19-ward-2852323" target="_blank">Link</a></td>
                <tr>
                <tr>
                    <td>The Christie Response Centre</th>
                    <td>special centre for patients on cancer treatment... </td>
                    <td>17th March 2020</td>
                    <td><a href="https://www.manchestereveningnews.co.uk/news/greater-manchester-news/christie-opens-response-centre-dedicated-17940815" target="_blank">Link</a></td>
                <tr>
                <tr>
                    <td>North West Nightingale - Manchester</td>
                    <td>750</th>
                    <td>17th April 2020</td>
                    <td><a href="https://mft.nhs.uk/2020/04/17/new-nhs-nightingale-hospital-north-west-opens/" target="_blank">Link</a></td>
                <tr>
                <tr>
                    <th scope="row">Total</th>
                    <th scope="row" colspan="3">865</th>
                <tr>
            </tbody>
        </table>

        <p>&nbsp;</p>

    <hr />
        <h3>Footnotes</h3>
            <ol>
                <li id="FootNote1">Taken from <a href="https://www.gov.uk/government/collections/slides-and-datasets-to-accompany-coronavirus-press-conferences" target="_blank">datasets that accompany UK coronavirus press conferences</a>
                </li>

                <li id="FootNote2">This chart represents the scenario where North West hospitals would be coping with both ‘normal operations’ for this time of year, plus COVID-19 patients.  However, at this time any 
                    non-urgent procedures are currently suspended.</li>

                <li id="FootNote3">Available Beds = Number of <a href="https://www.england.nhs.uk/statistics/statistical-work-areas/critical-care-capacity/critical-care-bed-capacity-and-urgent-operations-cancelled-2019-20-data/" target="_blank">
                    Critical Care Beds</a> recorded in Feb 2020 (this report has since been suspended due to pandemic) 712 + <a href="https://www.england.nhs.uk/statistics/statistical-work-areas/bed-availability-and-occupancy/bed-data-day-only/" target="_blank">
                    Available Day Beds</a> Jan-Mar 2020 1,649 + <a href="https://www.england.nhs.uk/statistics/statistical-work-areas/bed-availability-and-occupancy/bed-data-overnight/" target="_blank">
                    Available Night Beds</a> Jan-Mar 2020 18,629 + North West Recorded 'Nightingale' Hospitals and Wards 865.</li>

                <li id="FootNote4">Occupied Beds = Number of <a href="https://www.england.nhs.uk/statistics/statistical-work-areas/critical-care-capacity/critical-care-bed-capacity-and-urgent-operations-cancelled-2019-20-data/" target="_blank">
                    Occupied Critical Care Beds</a> recorded in Feb 2020 (this report has since been suspended due to pandemic) 577 + <a href="https://www.england.nhs.uk/statistics/statistical-work-areas/bed-availability-and-occupancy/bed-data-day-only/" target="_blank">
                    Occupied Day Bed</a> Jan-Mar 2020 1,380 + <a href="https://www.england.nhs.uk/statistics/statistical-work-areas/bed-availability-and-occupancy/bed-data-overnight/" target="_blank">
                    Occupied Night Beds</a> Jan-Mar 2020 15,523.</li>

                <li id="FootNote5">NHS England guidance recommends that each Hospital Trust not exceed 80% of their total capacity at any given time.  It is usually at around 82% that local media begins reporting on hospitals 
                    reaching capacity, such as with wintertime annual flu epidemics.</li>

                <li id="FootNote6">Northern Ireland typically reports 2 days behind all other UK areas.  So the number shown actually reflects 2 days ago. (<?php echo $myArray[2]['date']; ?>)</li>


            </ol>
  </div>

<?php include('footer.php'); ?>