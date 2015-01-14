var loadPreviousGamesCtrl = function ($scope, $modalInstance, f_data) {

    $scope.copies=f_data.copies;
    
    $scope.initData = function() {
       $(".wait").hide();
       $(".saving").hide();
       $('.scroll-pane').jScrollPane();
       
       setPrevSelect();
    };
    
    var loadGameID=0;
    
    setPrevSelect = function() {
      $(".previous_game").unbind("click");
      $(".previous_game").click(function(){
         var copyID=$(this).attr("copyID");
         $(".previous_game").removeClass("selected");
         $(".game_prev_"+copyID).addClass("selected");
         loadGameID=copyID;
      });
    };
    
    $scope.setGameForApply = function(id) {
 
    };
    
    $scope.cancel = function () {
          $modalInstance.dismiss('cancel');
    };
    
    $scope.applyLoadPrev = function() {
        $modalInstance.close(loadGameID);
    };
};

