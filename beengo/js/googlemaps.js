var map = null;
var geocoder = null;
 
function mapInit(){
    geocoder = new google.maps.Geocoder();
    var element = document.getElementById("googlemaps");
    var latlng = new google.maps.LatLng(35.682956,139.766092);
    var options = {
        zoom: 15,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP,
    };
    map = new google.maps.Map(element,options);
}

function searchMap(){
    var input = document.getElementById("map_location");
    var options = {
        address: input.value,
        language: 'ja',
        region: 'jp'
    };
    var func = function(results,status){
        if (status == google.maps.GeocoderStatus.OK){
            var p = results[0].geometry.location;
            map.setCenter(p);
            var opts = {
                position: p,
                map: map,
                title: results[0].formatted_address
            };
            var marker = new google.maps.Marker(opts);
            var infoopt = {
                content: results[0].formatted_address
            };
            var info = new google.maps.InfoWindow(infoopt);
            info.open(map,marker);
        }
    };
    geocoder.geocode(options,func);
}