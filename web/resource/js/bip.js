$(function() {
    var map;
    var bips;
    var Bip = Backbone.Model.extend({  
        defaults: {
            Person: "",
            Lat: 0,
            Lon: 0,
            Accuracy: 0
        },
        url: '/bips'
    }); 

    var BipCollection = Backbone.Collection.extend({
        model: Bip,
        url: '/bips'
    });

    var MapView = Backbone.View.extend({
        initialize: function () {
            updateLocalBip();

        },
        render: function() {
            myLatLng = new google.maps.LatLng(51.49, -0.12);
            var myOptions = {
              zoom: 10,
              center: myLatLng,
              mapTypeId: google.maps.MapTypeId.ROADMAP
            }
            
            map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

            bips.each(function (bip) {

                var bipLatlng = new google.maps.LatLng(bip.get('Lat'), bip.get('Lon'));

                var marker = new MarkerWithLabel({
                    position: bipLatlng, 
                    map: map, 
                    animation: google.maps.Animation.DROP,
                    labelContent: bip.get('Person') + "(" + bip.get('TimeSince') + ")",
                    labelAnchor: new google.maps.Point(22, -10),
                    labelClass: "labels"
                });
            });
        }
    });

    function updateLocalBip() {
        var localBip = new Bip;
        delete localStorage.name;
        //localStorage.group = null;
        if (!hasName()) {
            getName();
        }

        if (!hasGroup()) {
            getGroup();
        }
        localBip.set({Person: localStorage.name});

        navigator.geolocation.getCurrentPosition(function (position) {
            localBip.set({
                Lat: position.coords.latitude,
                Lon: position.coords.longitude,
                Accuracy: position.coords.accuracy
            });
            
            localBip.save();
            bips.fetch({
                success: function(){
                    mapView.render();
                }
            });
        }, onGeoLocationError);
    }

    function hasName() {
        return localStorage.name;
    }

    function getName() {
        var name = prompt("Please enter your username");
        if (!name) {
            getName();
        }

        localStorage.name = name;
    }

    function hasGroup() {
        return localStorage.group;
    }

    function getGroup() {
        var group = prompt("Please enter your group");
        if (!group) {
            getGroup();
        }

        localStorage.group = group;
    }

    var mapView = new MapView({el: $('#map-area')});

    function onGeoLocationError(error) {
      switch(error.code) {
        case error.TIMEOUT:
          alert ('Timeout');
          break;
        case error.POSITION_UNAVAILABLE:
          alert ('Position unavailable');
          break;
        case error.PERMISSION_DENIED:
          alert ('Permission denied');
          break;
        case error.UNKNOWN_ERROR:
          alert ('Unknown error');
          break;
      }
    }

    $(document).ready(function() {
        bips = new BipCollection;
    });
});