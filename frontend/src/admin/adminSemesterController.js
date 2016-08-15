angular.module('school-office').controller('AdminSemesterController',
[
  "NgTableParams",
  "$scope",
  "RestService",
function(NgTableParams,$scope,rest){
    rest.semesters.query(function(semesters){
        $scope.semestersTable = new NgTableParams({}, { dataset: semesters});
    });
}]);
