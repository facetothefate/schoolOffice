angular.module('school-office').controller('AdminCourseController',
[
  "NgTableParams",
  "$scope",
  "RestService",
function(NgTableParams,$scope,rest){
    rest.courses.query(function(courses){
        $scope.coursesTable = new NgTableParams({}, { dataset: courses});
    });
}]);
