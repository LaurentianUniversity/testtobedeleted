var app = angular.module('jiraReport', []);

app.controller('reportController', ['$scope', '$http', function($scope, $http) {

    var username = 'IY_Singh';
    $scope.leadProjects = [];

    //Date Information
    var currentDate = new Date();
    var currentMonth = currentDate.getMonth();
    var currentDay = currentDate.getDay();
    var currentYear = currentDate.getFullYear();

    var getQueryVariable = function(variable) {
        var query = window.location.search.substring(1);
        var vars = query.split("&");
        for (var i=0;i<vars.length;i++) {
            var pair = vars[i].split("=");
            if (pair[0] == variable) {
                return pair[1];
            }
        }
        alert("Query Variable "+variable+" not found");
    };


    $http.get('queryIssues.php?username='+username).success(function(response){
        console.log(response);
        $scope.totalIssues = response.total;
        $scope.issues = response.issues;

        //console.log($scope.issues);
    });

    $http.get('queryProjects.php').success(function(response){
        for (var i = 0; i < response.length; ++i) {
            if (response[i].lead.name == username)
                $scope.leadProjects.push(response[i].name);
        }

        $scope.totalProjects = $scope.leadProjects.length;
    });
}]);