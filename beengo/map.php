<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
<script type="text/javascript" src="js/jquery-2.0.2.min.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="js/googlemap.js"></script>
<?php
$latlng =array();
 
$url = "http://maps.google.com/maps/api/geocode/json?address='大阪駅'&sensor=false";
// レスポンスを取得
$res = file_get_contents($url);
// JSON形式から連想配列へ変換
$res_array = json_decode($res, TRUE);

//x座標とy座標を配列に格納
// $latlng['lat'] = $res_array['results']['0']['geometry']['location']['lat'];
// $latlng['lng'] = $res_array['results']['0']['geometry']['location']['lng'];
$latlng['lat'] = $res_array['results']['0']['geometry']['location']['lat'];
$latlng['lng'] = $res_array['results']['0']['geometry']['location']['lng'];
?>

<script>




$(function(){
  $('#googleMap').gmap(<?php echo $latlng['lat']; ?>, <?php echo $latlng['lng']; ?>);
});

</script>    

</head>
<body>
    <div id="googleMap" style="width: 600px;height: 460px;"></div>
</body>
</html>

