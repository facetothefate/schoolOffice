angular.module('school-office').controller('AdminStudentController',
[
  "NgTableParams",
  "$scope",
  "RestService",
function(NgTableParams,$scope,rest){
    rest.students.query(function(students){
        $scope.studentTable = new NgTableParams({}, { dataset: students});
    });
}]);

angular.module('school-office').controller('AdminStudentDetailController',
[
  "NgTableParams",
  "$scope",
  "$routeParams",
  "RestService",
function(NgTableParams,$scope,$routeParams,rest){
  if($routeParams.action==="edit"){
    rest.students.get({"id":$routeParams.studentNumber},function(student){
        student.birthday = new Date(student.birthday);
        $scope.student = student;
    });
    $scope.edit = true;

  }else{
    $scope.edit = false;
  }
  //$scope.studentTranscriptsTable = new NgTableParams({}, { dataset: });
}]);
