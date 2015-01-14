var app = angular.module('wheeldoApp', [ 'ui.bootstrap', 'angularFileUpload', 'googlechart']);
//This configures the routes and associates each route with a view and a controller
app.config(function ($routeProvider) {
    $routeProvider
        .when('/',
            {
                controller: 'gameboardController',
                templateUrl: '/mainBoard.php'
            })
        .when('/MyGames',
            {
                controller: 'myGamesController',
                templateUrl: '/app/partials/my_games.html'
            })
        .when('/teams',
            {
                controller: 'teamsController',
                templateUrl: '/app/partials/teams.html'
            })
        .when('/users_logs',
            {
                controller: 'usersLogsController',
                templateUrl: '/app/partials/users_logs.html'
            }) 
        .when('/users_manage',
            {
                controller: 'usersManageController',
                templateUrl: '/app/partials/users_manage.html'
            }) 
        .when('/accounts',
            {
                controller: 'accountsController',
                templateUrl: '/app/partials/accounts.html'
            }) 
        .when('/developer',
            {
                controller: 'developerController',
                templateUrl: '/app/partials/developer.html'
            })
        .when('/createGame/:app/:copyID',
            {
                controller: 'createGameController',
                templateUrl: '/app/partials/createGame.php'
            })
        .when('/editGame/:app/:copyID',
            {
                controller: 'createGameController',
                templateUrl: '/app/partials/editGame.php'
            })
        .when('/publish/:app/:copyID',
            {
                controller: 'publishController',
                templateUrl: '/app/partials/publish.html'
            })
        .when('/report/:app/:copyID',
            {
                controller: 'reportController',
                templateUrl: '/app/partials/report.html'
            })
        .when('/admins',
            {
            controller: 'adminsController',
            templateUrl: '/app/partials/admins.html'
            })
        .when('/purchase',
            {
            controller: 'purchaseController',
            templateUrl: '/app/partials/purchase.html'
            })
        
        
        .otherwise({ redirectTo: '/' });
});

app.directive('testdirective', function() {
  return function(scope, element, attrs) {
    //console.log('ROW: index = ', scope.$index);
    scope.$watch('$last',function(v){
      if (v) {
          //setCheckAll();
      }
    });
    
  };
});


app.directive('selectBox', function() {
 return {
  restrict: 'E',
  link: function() {
   return $(window).bind("load", function() {
    //this will make all your select elements use bootstrap-select
    return $('select').selectpicker();
   });
  }
 };
}); 




app.directive('repeatDone', function() {
    return function(scope, element, attrs) {
        if (scope.$last) { // all are rendered
            scope.$eval(attrs.repeatDone);
        }
    }
});

app.directive('contentLoad', function() {
    return function(scope, element, attrs) {

            scope.$eval(attrs.contentLoad);

    }
});

app.filter('slice', function() {
  return function(object_arr, start, end) {
    return object_arr.slice(start, end);
  };
});

app.directive('myLoad', function() {                                               
  return function(scope, iElement, iAttrs, controller) {                        
    scope.$watch(iAttrs.pgVisible, function(value) {                            
      iElement.bind('load', function(evt) {                                     
        scope.$apply(iAttrs.pgLoad);                                            
      });                                                                       
    });                                                                         
  };                                                                            
});


app.directive('uiColorpicker', function() {
    return {
        restrict: 'E',
        require: 'ngModel',
        scope: false,
        replace: true,
        template: "<span><input class='input-small' /></span>",
        link: function(scope, element, attrs, ngModel) {
            var input = element.find('input');
            var options = angular.extend({
                color: ngModel.$viewValue,
                change: function(color) {
                    scope.$apply(function() {
                      ngModel.$setViewValue(color.toHexString());
                    });
                }
            }, scope.$eval(attrs.options));
            
            ngModel.$render = function() {
              input.spectrum('set', ngModel.$viewValue || '');
            };
            
            input.spectrum(options);
        }
    };
});

