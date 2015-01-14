var addTeamCtrl = function ($scope, $modalInstance, f_data) {

    $scope.team={
        name:""
    }

    $scope.cancel = function () {
          $modalInstance.dismiss('cancel');
    };
    
    $scope.createTeam = function() {
        if($scope.team.name) {
            $modalInstance.close($scope.team);
        }
        else {
            alert("Please fill team name!");
        }

    };
};


var selectTeamCtrl = function ($scope, $modalInstance, f_data) {

    $scope.teams=f_data.teams;
    $scope.selected_team=0;

    $scope.cancel = function () {
          $modalInstance.dismiss('cancel');
    };
    
    $scope.selectTeam = function() {
        $scope.selected_team=$("#selected_team_val").val();
        if($scope.selected_team) {
            $modalInstance.close($scope.selected_team);
        }
        else {
            alert("Please select team!");
        }

    };
};


