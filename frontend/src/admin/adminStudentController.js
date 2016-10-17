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
  "$mdMedia",
  "$mdDialog",
  "$routeParams",
  "RestService",
function(NgTableParams,$scope,$location,$mdMedia,$mdDialog,$routeParams,rest){
    function getMysqlFormatDate(date){
        return date.getFullYear() + '-' +date.getMonth() + '-' + date.getDate();
    }
    function handleError(errorData){
        $scope.loading = false;
        $mdDialog.show(
          $mdDialog.alert()
            .clickOutsideToClose(true)
            .title('Error')
            .textContent("Error:"+errorData.data.error+"\n"+" Please Contact the admin")
            .ariaLabel('Error')
            .ok('Ok!')
        );
    }
    function getSelections(student){
        var student = $scope.student;
        rest.course_selections.get({studentNumber:student.student_number},function(selections){
            for(var i=0;i<selections.length;i++){
                switch (selections[i].status) {
                    case '0':
                        selections[i].removable = true;
                        selections[i].status_text = 'Under review';
                        break;
                    case '1':
                        selections[i].status_text = 'Selected';
                        break;
                    case '2':
                        selections[i].status_text = 'Rejected';
                        break;
                    case '3':
                        selections[i].status_text = 'Dropped';
                        break;
                    default:
                        selections[i].removable = false;

                }
                selections[i].code = selections[i].code.trim();
            }
            $scope.studentCourseSelectionTable = new NgTableParams({}, { dataset:selections });
        });
    }
    if($routeParams.action==="edit"){
        rest.students.get({"id":$routeParams.studentNumber},function(student){
            student.birthday = new Date(student.birthday+' 00:00:00');
            student.enter_date = new Date(student.enter_date+' 00:00:00');
            student.enter_grade = parseInt(student.enter_grade);
            $scope.student = student;
            getSelections();
        });
        rest.semesters.query(function(semesters){
            $scope.semesters = semesters;
        });
        //$scope.selectedSemester = null;
        $scope.edit = true;
        $scope.submit=function(){
            //alert("Not supported yet");
            if($scope.loading){
                return;
            }
            $scope.loading = true;
            rest.students.admin_update({},$scope.student,function(){
                $scope.loading = false;
            },handleError);
        };
        $scope.drop = function(selection){
            rest.course_selections.drop({
                student_number:$routeParams.studentNumber,
                semester_id:selection.semester_id,
                code:selection.code,
            },function(){
                getSelections();
            },function(){
                alert("Drop failed, try agian later");
            });
        };
        //dialog controller
        function SelectionDialogCtrl($scope,$mdDialog,semester,student){
            var that = this;
            this.loading = false;
            rest.courses.query(function(courses){
                $scope.coursesTable = new NgTableParams({}, { dataset:courses});
                //console.log($scope.coursesTable);
            });
            this.selection = [];
            this.selectRow = function(item){
                item.selected = !item.selected;
            };
            this.semester_name = semester.semester;
            this.select = function(){
                //$scope.coursesTable
                for(var i=0; i<$scope.coursesTable.data.length;i++){
                    if($scope.coursesTable.data[i].selected){
                        var data = {};
                        data.semester_id = semester.id;
                        data.course_code = $scope.coursesTable.data[i].code;
                        data.student_number = student.student_number;
                        this.selection.push(data);
                    }
                }
                console.log(this.selection);
                that.loading = true;
                rest.course_selections.save(this.selection,function(){
                    $mdDialog.hide({
                        selection:this.selection
                    });
                },function(error){
                    that.loading = false;
                    if(error.data){
                        that.error_msg = error.data.error;
                    }else{
                        that.error_msg = "Error occured !"
                    }
                    that.selection = [];
                });
                /**/
            };
            this.cancel = function(){
                $mdDialog.cancel();
            };
        }
        $scope.openCourseSelection = function(selectedSemester){
            var useFullScreen = ($mdMedia('sm') || $mdMedia('xs'))  && $scope.customFullscreen;
            $mdDialog.show({
              controller: SelectionDialogCtrl,
              controllerAs:"ctrl",
              templateUrl: 'templates/admin/adminStudentCourseSelectDialog.html?v='+Math.random(),
              parent: angular.element(document.body),
              clickOutsideToClose:false,
              locals:{
                  semester:selectedSemester,
                  student:$scope.student,
              },
              fullscreen: useFullScreen
            })
            .then(function(selections) {
                getSelections();
            },function(){
                alert("Select failed, please Try agian later");
            });
            $scope.$watch(function() {
                return $mdMedia('xs') || $mdMedia('sm');
            }, function(wantsFullScreen) {
                $scope.customFullscreen = (wantsFullScreen === true);
            });
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
            },handleError);
        }
    }
  //$scope.studentTranscriptsTable = new NgTableParams({}, { dataset: });
}]);
