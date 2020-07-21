<?php


//set map api url
$url = "https://api.coronavirus-staging.data.gov.uk/v1/data?filters=areaType=ltla;areaName=Wigan&structure=%7B%22areaType%22:%22areaType%22,%22areaName%22:%22areaName%22,%22areaCode%22:%22areaCode%22,%22date%22:%22date%22,%22newCasesBySpecimenDate%22:%22newCasesBySpecimenDate%22,%22cumCasesBySpecimenDate%22:%22cumCasesBySpecimenDate%22%7D&format=json";

//call api
$json = file_get_contents($url);
$json = json_decode($json);

echo $json;

?>