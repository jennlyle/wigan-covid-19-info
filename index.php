<?php

$configs = include('config.php');
include('db.php');
$db = new db($configs['servername'], $configs['username'], $configs['password'], $configs['database']);

// ---- DB Selection North West New Hosptialisation Trends ---//

$myQuery = "SELECT `people_in_hospital`.`north_west`
  FROM `people_in_hospital` 
  ORDER BY `people_in_hospital`.`date` DESC LIMIT 21";

$fields = $db->query($myQuery)->fetchAll();

$count = 0;
foreach ($fields as $field) {   
    $NWHosp[$count] = $field['north_west'];
    $count++;
}
unset($fields);
unset($field);

// ---- DB Selection Wigan Lab Results Trends ---//

$myQuery = "SELECT `confirmed_labs`.`nw_labs`
FROM `confirmed_labs`
ORDER BY `confirmed_labs`.`date` DESC LIMIT 31";

$fields = $db->query($myQuery)->fetchAll();

$count = 0;
foreach ($fields as $field) {   
  $newLabs[$count] = $field['nw_labs'];
  $count++;
}
unset($fields);
unset($field);

$db->close();

// ---- Calculating North West New Hosptialisation Trends ---//

for ($i = 0; $i < count($NWHosp); $i++){
  if ($i === 0){
    $netAdmitDischarge[$i] = 0;
  }
  else {
    $netAdmitDischarge[$i] = $NWHosp[$i - 1] - $NWHosp[$i];
  }
}

$mySum = $netAdmitDischarge[1] + $netAdmitDischarge[2] + $netAdmitDischarge[3] + $netAdmitDischarge[4] + $netAdmitDischarge[5] + $netAdmitDischarge[6] + $netAdmitDischarge[7];
$dischargeText = "<h5><p style=\"color:green\">North West hospital discharges vs admissions are rising (good)</p></h5>";
if ((array_sum($netAdmitDischarge)/20) < ($mySum/7)) {
  $dischargeText = "<h5><p style=\"color:red\">North West hospital admissions are rising (bad)</p></h5>";
}

// ---- Calculating Wigan Confirmed Labs Trends ---//
// Need to ignore last 10 days
$myNewLabs = array_slice($newLabs, 10);
//var_dump($myNewLabs);
$mySum = $myNewLabs[1] + $myNewLabs[2] + $myNewLabs[3] + $myNewLabs[4] + $myNewLabs[5] + $myNewLabs[6] + $myNewLabs[7];
$labsText = "<h5><p style=\"color:green\">Wigan new lab cases are dropping (good)</p></h5>";

if ((array_sum($myNewLabs)/21) < ($mySum/7)) {
  $labsText = "<h5><p style=\"color:red\">Wigan new lab results are rising (bad)</p></h5>";
}

$Title = "Welcome to the Wigan COVID-19 Tracker";
$PageDescription = "Wigan based COVID-19 Tracker - get all of the latest data you need to make all of the right decisions";
$PageKeywords = "Welcome Page, Wigan, North West England, COVID-19, coronavirus, lockdown";

include('header.php');

/*
  TODOs

  - index dates in the database to see if that speeds queries

*/

?>
  </head>
  <body>
  <div class="container">
    <header><h1><?php echo $Title; ?></h1></header>

<?php include('navigation.php'); ?>

    <div class="container">
   
      <p>
        Welcome to the Wigan COVID-19 Tracker!  Obviously, you’re here for the Graphs and Charts, so here’s a summary:

        <h2>Is it Safe? (Current Trends)</h2>
        <table class="table table-hover">
          <tbody>
            <tr>
              <td>North West Hospitalisation Admissions vs Discharges</td>
              <td><?php echo $dischargeText; ?></td>
            </tr>
            <tr>
              <td>Wigan Lab Confirmed New Cases</td>
              <td><?php echo $labsText; ?></td>
            </tr>
            <!-- <tr>
              <td>Wigan R</td>
              <td>(up or down arrow)</td>
            </tr> -->
          </tbody>
        </table>

        <ul>
          <li>
            <h2><a href="people_in_hospital.php"">People in Hospital in the North West</a></h2> 
            <p>
              <ol>
                  <li>Includes summary of total people in hospital for COVID-19, comparing English Regions and UK Nations.</li>
                  <li>Includes an estimate of how close to capacity North West hospitals are, if the NHS was running under “normal” operations.</li>
                  <li>Includes a chart comparing recent daily Admissions and Discharges from North West hospitals of COVID-19 patients.</li>
              </ol>
            </p>
          </li>
          <li>
            <h2><a href="hospital_announced_deaths.php"">Hospital Announced Deaths</a></h2>
            <p>
              <ol>
                <li>Includes a comparison of Announced Hospital Deaths between Wrightington, Wigan, and Leigh NHS Foundation Trust and Manchester University NHS Foundation Trust.</li>
                <li>Includes a comparison for Wigan, the North West, and for England of Announced Hospital Deaths from COVID-19 per 100k of the population.</li>
              </ol>
            </p>
          </li>

          <li>
            <h2><a href="lab_confirmed_cases.php"">Lab Confirmed New Cases</a></h2>
            <p>
              <ol>
                <li>Includes confirmed COVID-19 Lab Test Results reported for individuals living in Wigan.</li>
                <li>Includes a comparison of the Cumulative Rate of Infection within Wigan, the North West, and England per 100k of the population.</li>
              </ol>
            </p>
          </li>

          <!-- <li>
            <h2><a href="wigan_r.php"">Wigan R</a></h2>
            <p>Includes my calculation for Wigan’s R compared against North West’s R and England’s R.</p>
          </li> -->

          <li>
            <h2>Other Information</h2>
            <p>
              <ol>
                <li><h3><a href="news.php"">News</a></h3>
                <p>...if you are interested in initiatives and additional details about this project as it grows.</p>
                <li><h3><a href="links.php"">Sources & Links</a></h3>
                <p>...to learn where I get my information from.</p>
                <li><h3><a href="about.php"">About</a></h3>
                <p>...more about me, as the person who put together this website.</p>
                <li><h3><a href="disclaimer.php"">Disclaimer</a></h3>
                <p>...the all-important health disclaimers.</p>
              </ol>
            </p>
          </li> 
        </ul>
      </p>

      <p>
        <h3>And Remember...</h3>
        <ul>
          <h5><li>Maintain Physical Distancing</li>
          <li>Wash Your Hands</li>
          <li>Wear a Face Cover in Enclosed Public Spaces</li>
          <li>Look After Your Neighbours</li>
          <li>STAY SAFE!</li></h5>
        <ul>
      </p>

    </div>

<?php include('footer.php'); ?>