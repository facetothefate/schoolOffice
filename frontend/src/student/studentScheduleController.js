angular.module('school-office').controller('StudentHomeController',
[
  "$scope",
  "RestService",
function($scope,rest){
    $scope.weekday=[
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday"
    ];
    rest.semesters.query(function(semesters){
        $scope.semesters = semesters;
    });
}]);
