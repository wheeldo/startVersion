var addPlayerCtrl = function ($scope, $modalInstance, f_data) {

    $scope.add_text="Add";
    if(tm) {
        $scope.add_text="Add and use 1 token";
    }
    
    
    
    $scope.empID=false;
    if(f_data.empID==true) {
        $scope.empID=true;
    }

    $scope.player={
        name:"",
        email:"",
        empID:""
    }

    $scope.cancel = function () {
          $modalInstance.dismiss('cancel');
    };
    
    $scope.addPlayer = function() {

        if((!$scope.empID && $scope.player.name && $scope.player.email) || ($scope.empID && $scope.player.name && $scope.player.email && $scope.player.empID)) {
            $modalInstance.close($scope.player);
        }
        else {
            alert("Please fill all the fileds!");
        }

    };
};