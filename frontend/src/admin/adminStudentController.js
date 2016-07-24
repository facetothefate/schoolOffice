angular.module('school-office').controller('AdminStudentController',
[
  "NgTableParams",
  "$scope",
function(NgTableParams,$scope){
  var students = [{
    "surname":"Lee",
    "given_names":"Mary",
    "oen":999888999,
    "student_number":111111,
    "gender":"F",
    "birth":777686400000,
    "date_entry":1220745600000,
    "diploma":"Ontario Secondary School Diploma",
    "address":'',
    "hoomroom":'',
    "OEN":'871-395-034'
  }];
  $scope.studentTable = new NgTableParams({}, { dataset: students});
}]);
