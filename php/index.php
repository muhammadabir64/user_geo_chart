<?php 
$db = mysqli_connect("localhost", "root", "", "regions");

$client = @unserialize(file_get_contents("http://ip-api.com/php/"));
if($client["status"] === "success"){
	mysqli_query($db, "UPDATE regions SET total_views=total_views+1 WHERE country_code='".$client["countryCode"]."';");
}
 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel= "stylesheet" href="resources/bootstrap.min.css">
    <title>PHP GeoChart</title>
</head>
<body>
<div class="container py-5">
<div class="card mx-auto" style="max-width: 500px;">
<div class="card-body">
<div id="regions_div"></div>
</div>
</div>
<table class="table">
<thead>
<tr>
  <th>#</th>
  <th>Country</th>
  <th>Code</th>
  <th>Total View</th>
</tr>
</thead>
<tbody>
<?php 
$sl = 0;
$query = mysqli_query($db, "SELECT * FROM regions WHERE total_views > 0 ORDER BY total_views DESC;");
while($data = mysqli_fetch_assoc($query)){
	$sl++; ?>
  <tr>
    <td><?php echo $sl; ?></td>
    <td><?php echo $data["country_name"]; ?></td>
    <td><?php echo $data["country_code"]; ?></td>
    <td><?php echo $data["total_views"]; ?></td>
  </tr>
<?php } ?>
</tbody>
</table>
</div>
<script src="resources/bootstrap.min.js"></script>
<script src="https://www.gstatic.com/charts/loader.js"></script>
<script>
      google.charts.load('current', {
        'packages':['geochart'],
      });
      google.charts.setOnLoadCallback(drawRegionsMap);

      function drawRegionsMap() {
        var data = google.visualization.arrayToDataTable([
          ["Country", "Total Views"],
<?php 
$chart_query = mysqli_query($db, "SELECT * FROM regions WHERE total_views > 0 ORDER BY total_views DESC;");
while($chart_data = mysqli_fetch_assoc($chart_query)){ ?>
        ["<?php echo $chart_data['country_code']; ?>", <?php echo $chart_data["total_views"]; ?>],
<?php } ?>
        ]);
        var options = {};

        var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));

        chart.draw(data, options);
      }
</script>
</body>
</html>