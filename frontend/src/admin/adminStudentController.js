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
  "$location",
  "$mdDialog",
  "$routeParams",
  "RestService",
function(NgTableParams,$scope,$location,$mdDialog,$routeParams,rest){
    function getMysqlFormatDate(date){
        return date.getFullYear() + '-' +date.getMonth() + '-' + date.getDate();
    }
    if($routeParams.action==="edit"){
        rest.students.get({"id":$routeParams.studentNumber},function(student){
            student.birthday = new Date(student.birthday+' 00:00:00');
            student.enter_date = new Date(student.enter_date+' 00:00:00');
            $scope.student = student;
            rest.course_selections.get({studentNumber:student.student_number},function(selections){
                for(var i=0;i<selections.length;i++){
                    switch (selections[i].status) {
                        case '0':
                            selections[i].removable = true;
                            selections[i].status_text = 'Under review';
                            break;
                        case '1':
                            selections[i].status_text = 'Selected';
                        default:
                            selections[i].removable = false;

                    }
                    selections[i].code = selections[i].code.trim();
                }
                $scope.studentCourseSelectionTable = new NgTableParams({}, { dataset:selections });
            });
        });
        $scope.edit = true;
        $scope.submit=function(){
            alert("Not supported yet");
        };
    }else{
        $scope.edit = false;
        $scope.student={
            diploma_text:"Ontario Secondary School Diploma",
            diploma:1,
        }
        $scope.submit = function(){
            if($scope.loading){
                return;
            }
            $scope.loading = true;
            var student={};
            for(var key in $scope.student){
                student[key]=$scope.student[key];
            }
            student.enter_date = $scope.student.enter_date;
            student.birthday = $scope.student.birthday;
            rest.students.save({},student,function(){
                $scope.loading = false;
                $location.path('/adminStudents');
            },function(errorData){
                $scope.loading = false;
                $mdDialog.show(
                  $mdDialog.alert()
                    .clickOutsideToClose(true)
                    .title('Error')
                    .textContent("Error:"+errorData.data.error+"\n"+" Please Contact the admin")
                    .ariaLabel('Error')
                    .ok('Ok!')
                );
            });
        }
    }
  //$scope.studentTranscriptsTable = new NgTableParams({}, { dataset: });
}]);
