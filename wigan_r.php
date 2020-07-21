<?php

$Title = "Wigan R | Wigan COVID-Tracker";
$PageDescription = "Includes my calculation for Wigan’s R compared against North West’s R and England’s R.";
$PageKeywords = "R, England, North West, Wigan, COVID-19, coronavirus, lockdown";


$configs = include('config.php');
$constants = include('constants.php');
//$ukPopulation = $constants['england_population'] + $constants['scotland_population'] + $constants['wales_population'] + $constants['nireland_population'];
include('db.php');

$db = new db($configs['servername'], $configs['username'], $configs['password'], $configs['database']);

/*
For my R I've got to consider all rates of infection that we know about today.  Hypothesize what R was then to pick up a few more cases.

Let's come up with some rules!

R likes to work off of 5 infectious days from labs.  Labs reporting is spotty.

We have 


*/


// n = Date of Infection
// Total Cases (TC) = Daily Reported Total Cumulative Confirmed Cases
// New Cases (NC) = Today's number of new cases Minus yesterday's number of new cases
// AI - Average Infection Group -- sum of NC / 5 Infectious days
// Infection Ratio ( R) = NC / AI
// A-Dir is the weekly rolling average

$myQuery = "SELECT `confirmed_labs`.`date`, 
`confirmed_labs`.`england_labs`, 
`confirmed_labs`.`nw_labs`, 
`confirmed_labs`.`wigan_labs`,

`hospital_announced_deaths`.`england`,
`hospital_announced_deaths`.`north_west`,
`hospital_announced_deaths`.`wigan`,

`people_in_hospital`.`east_of_england`,
`people_in_hospital`.`london`,
`people_in_hospital`.`midlands`,
`people_in_hospital`.`north_east`,
`people_in_hospital`.`north_west`,
`people_in_hospital`.`south_east`,
`people_in_hospital`.`south_west`

FROM `confirmed_labs`, `hospital_announced_deaths`, `people_in_hospital`

WHERE (`confirmed_labs`.`date` = `hospital_announced_deaths`.`date`) AND 
(`hospital_announced_deaths`.`date` = `people_in_hospital`.`date`)

ORDER BY date DESC";

$fields = $db->query($myQuery)->fetchAll();


$myQuery = "SELECT * FROM ons_deaths_wigan ORDER BY date DESC LIMIT 3";
$fields1 = $db->query($myQuery)->fetchAll();


$db->close();

$values = [];
$wiganCummLabs = 0;
foreach ($fields as $field) {   

    $england_hospitals = $field['east_of_england'] + $field['london'] + $field['midlands'] + $field['north_east'] + $field['north_west'] + $field['south_east']+ $field['south_west'];

    array_push($values, array("date" => $field['date'], 
    "england_labs" => $field['england_labs'],
    "nw_labs" => $field['nw_labs'], 
    "wigan_labs" => $field['wigan_labs'],
    "england_deaths" => $field['england'],
    "nw_deaths" => $field['north_west'], 
    "wigan_deaths" => $field['wigan'],
    "england_hospitals" => $england_hospitals,
    "nw_hospitals" => $field['north_west']
    ));

    $wiganCummLabs = $wiganCummLabs + $field['wigan_labs'];
}

$countArrayLength = count($values);

$onsData = [];
foreach ($fields1 as $field) {
    
    $total = $field['care_home'] + $field['elsewhere'] + $field['home'] + $field['hospice'] + $field['hospital']+ $field['other'];

    array_push($onsData, array("date" => $field['date'], 
    "total" => $total
    ));


}
$countArrayLength1 = count($onsData);

include('header.php');
?>


</head>
  <body>

  <div class="container">
    <div class="container">
      <header><h1><?php echo $Title; ?></h1></header>

<?php include('navigation.php'); ?>

<div class="container">
    <p>&nbsp;</p>

<?php

var_dump($values);
var_dump($onsData);

// Wigan, number of new cases 10 days ago
$wiganCases = $values[9]['wigan_labs'];
$TC = $wiganCummLabs;
$NC = $values[9]['wigan_labs'];



?>
    <h2>Comments</h2>
    <p>
        Calculating R is more or less about… 
    </p>
    <p>
        (1)	identifying your current infectious population and consider how, once they show symptoms, 
        may have been infectious for a certain number of days before.
    </p>
    <p>
        (2)	Identifying your infectious population from a week ago, how they may have been infectious for 
        a certain number of days before.
    </p>
    <p>
        (3)	Dividing one over the other to see if there’s growth in infection, or if there’s shrinking in 
        infection.
    </p>
    <p>
        While I’m grateful for the transparency in reporting in many areas we have, a lot of that reporting 
        is ‘slow’ so I have to pull data from a few days prior in order to have accurate numbers.  What’s 
        more, calculating R really gives the snapshot of the infection rate 2-3 weeks ago.  And if, like me, 
        you live in an area where folks don’t appear to take physical distancing seriously, you can almost 
        guarantee that your local R will be slightly higher than what’s reported even on academic websites.

    </p>

    <table class="table table-hover">
        <thead>
            <th scope="col">Scope</th>
            <th scope="col">R (median)</th>
            <th scope="col">R (low)</th>
            <th scope="col">R (high)</th>
        </thead>
        <tbody>
          <tr>
            <th scope="row">Wigan</th>
            <td><?php echo number_format($WiganR - ($NWR - $constants['current_NW_R']), 2) ; ?></td>
            <td><?php echo number_format($WiganR - (($NWR - $constants['current_NW_R']) * 2), 2) ; ?></td>
            <td><?php echo number_format($WiganR, 2, '.', ''); ?></td>
          <tr>
          <tr>
          <th scope="row">North West</th>
            <td><?php echo $constants['current_NW_R'] ; ?></td>
            <td><?php echo number_format($constants['current_NW_R'] - ($NWR - $constants['current_NW_R']), 2) ; ?></td>
            <td><?php echo number_format($NWR, 2, '.', ''); ?></td>
          <tr>
          <tr>
          <th scope="row">England</th>
            <td><?php echo number_format($englandR - ($NWR - $constants['current_NW_R']), 2) ; ?></td>
            <td><?php echo number_format($englandR - (($NWR - $constants['current_NW_R']) * 2), 2) ; ?></td>
            <td><?php echo number_format($englandR, 2, '.', ''); ?></td>
          <tr>
        </tbody>
    </table>

</div>

<hr />
    <h3>Footnotes</h3>
        <ol>
            <li>Data collected from <a href="https://coronavirus.data.gov.uk/" target="_blank">
                Gov.UK's Coronavirus Dashboard</a>. The most recent 5 days will be provisional data as some trusts take a few days to report up their numbers.
            </li>
            <li>
                Calculation of R is based off of the University of Manchester model, <a href="https://onlinelibrary.wiley.com/doi/epdf/10.1111/ijcp.13528" target="_blank">described in this paper</a>.
            </li>

            <li>
                Wigan R cannot be calculated with as much accuracy as North West R and England R because I do not have access to the current number of people in hospital with COVID-19 within WWL. 
                I can get those numbers for the North West and for England, but not for WWL all by itself.
            </li>

        </ol>
</div>

<?php include('footer.php'); ?>