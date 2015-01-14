app.controller('myGamesController', function ($scope,$http, WheeldoService) {

    $scope.show_remove=false;
    if(uk>4)
        $scope.show_remove=false;

    $scope.init = function() {
        var publishedApps=getPublishedApps();
        $scope.published_apps=publishedApps.copies;
        var market_apps=getMarketApps();
        $scope.market_apps=market_apps.apps;
        var categories=getGameCategories();
        $scope.categories=categories.categories;
        set_menagers_panel();
        setFeedback();
        setDDMenu();

        $scope.finish_load='loaded';
    };
    
    
    $scope.initMarket = function() {
        //alert("done");
        setEventLog();
        setViewMore();
        setWheeldoPopUp();
    };
    
    $scope.playNow = function(appID) {
        wait();

        // send data to app and get the link //
        $.ajax({
                type: "post",
                url: DATA_PATH+get_demo_url,
                data:{
                    appID:appID
                },
                success: function(data, textStatus, jqXHR) {
                    //alert("done");
                    OpenInNewTab(data)
                    //
                }
        });
    }
    
    
    
    $scope.createGame = function(index) {

        var app=$scope.market_apps[index];
        if(app.edit_in_service=="1") {
            window.location.href="/#/createGame/"+app.appID+"/0";
            return;
        }
        else {
            
        }
        
        
        
    };

});

