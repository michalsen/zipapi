<?php

// USING RB for Rapid Development
require 'rb.php';
R::setup("mysql:host=localhost; dbname=$DATABASE, $USER, $PASS");

// TODO: AN API KEY SHOULD BE REQUIRED
$zips = explode('/', $_REQUEST['zipcode']);

$points = [];
foreach($zips as $zip) {
  $zipcode = R::find('zipcodes',' zipcode = ? ',
                  array($zip)
                 );
  $points[] = display($zipcode, $zips);
}


// CALCULATE and RETURN DISTANCE
if (count($points) > 1) {
echo json_encode(['miles', calc_distance($points[0][0], $points[1][0])]);
 }
  else {
    // RETURN BASIC INFORMATION ON SINGLE ZIPCODE
    echo json_encode($points);
  }


function display($zip, $zips) {
  if (count($zip) == 0) {
    echo "EMPTY\n\n";
    return;
  }

$point = [];
$i=0;
foreach ($zip as $row) {
   if (count($zips) == 1) {
       $point = $row;
     }
      else {
        $point[$i] = [$row->lat, $row->lon];
     }
     $i++;
  }

  return $point;

}



function calc_distance($point1, $point2)
{

    $radius      = 3958;      // Earth's radius (miles)
    $deg_per_rad = 57.29578;  // Number of degrees/radian (for conversion)

    $distance = ($radius * pi() * sqrt(
                ($point1[0] - $point2[0])
                * ($point1[0] - $point2[0])
                + cos($point1[0] / $deg_per_rad)  // Convert these to
                * cos($point2[0] / $deg_per_rad)  // radians for cos()
                * ($point1[1] - $point2[1])
                * ($point1[1] - $point2[1])
        ) / 180);

    return $distance;  // Returned using the units used for $radius.
}
