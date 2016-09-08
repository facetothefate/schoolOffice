angular.module('school-office').controller('StudentHomeController',
[
  "$scope",
  "RestService",
  "ColorService",
  "$mdMedia",
  "$mdDialog",
  "NgTableParams",
function($scope,rest,color,$mdMedia,$mdDialog,NgTableParams){
    function getCondtionText(condition){
        var res = {
            text:"",
            grades:[],
        };
        var detail_text = "For grade " ;
        var grade = condition.grade;
        //handle grade conditons
        if(grade.indexOf(',')!==-1){
            var grades = grade.split(',');
            detail_text += grades[0]+" to "+grades[1];
            /*if(grades.length==2){
                For grade
            }*/
        }else{
            detail_text +=  grade;
        }
        detail_text += " ";
        //handle credit condition
        var credit = condition.credit;
        var op = credit.substring(0,1);
        var credit_value = credit.substr(1);
        if(op=="="){
            detail_text += "must have "+ credit_value + " credit(s) per grade";
        }else if(op=="+"){
            detail_text += "must have total "+ credit_value + " credit(s)";
        }else if(op=="-"){
            detail_text += "at least have " + credit_value + " credit(s)";
        }else if(op=="<"){
            detail_text += "at most have " + credit_value + " credit(s) as addtional credits";
        }
        res.text = detail_text;
        res.grades = getGradeList(condition);
        return res;
    }
    function getGradeList(condition){
        var res  = [];
        var grade = condition.grade;
        //handle grade conditons
        if(grade.indexOf(',')!==-1){
            var grades = grade.split(',');
            for(var i = parseInt(grades[0]);i<=parseInt(grades[1]);i++){
                res.push(i);
            }
        }else{
            res = [parseInt(grade)];
        }
        return res;
    }
    function renderHome(){
        if(!$scope.token||!$scope.token){
            $scope.student = null;
            $scope.conditions = null;
            return;
        }
        rest.student_username.get({username:$scope.token.username},function(student){
            $scope.student = student;
            var enterDate = new Date(student.enter_date);
            var enterYear = enterDate.getFullYear();
            var enterMonth = enterDate.getMonth()+1;
            var enterGrade = parseInt(student.enter_grade);
            var currentGrade = enterGrade;
            var currentDate = new Date();
            var currentYear = currentDate.getFullYear();
            var currentMonth = currentDate.getMonth()+1;
            if(enterYear == currentYear){
                if(enterMonth<9&&currentMonth>=9){
                    currentGrade ++;
                }
            }else if(enterYear < currentYear){
                if(enterMonth<9){
                    currentGrade ++;
                }
                if(currentMonth>=9){
                    currentGrade ++;
                }
                if(currentYear - enterYear>1){
                    currentGrade += currentYear - enterYear;
                }
            }
            $scope.currentGrade = currentGrade;

            rest.semesters_open.get({},function(semester){
                $scope.semester = semester;
            });
            //then get all the conditons
            rest.conditions.get({diploma:1},function(conditions){
                var clean_conditions = [];
                var cate_index = {};
                var index;
                for(var i = 0;i<conditions.length;i++){
                    var cate_id = conditions[i].so_course_categories_id;
                    var index = cate_index[cate_id];
                    if(!index && index!==0){
                        clean_conditions.push({
                            details:[getCondtionText(conditions[i])],
                            conditions:[conditions[i]],
                            category:{
                                title:conditions[i].title,
                                id:cate_id,
                            },
                            color:color.getLightColor(i),
                            selections:[]
                        });
                        index = clean_conditions.length-1;
                        cate_index[cate_id] = index;
                    }else{
                        var condition = clean_conditions[cate_index[cate_id]];
                        condition.details.push(getCondtionText[i]);
                        condition.conditions.push(condition[i]);
                    }
                }
                rest.course_selections.get({studentNumber:$scope.student.student_number},function(selections){
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
                        var cate_id = parseInt(selections[i].category_id);
                        var index = cate_index[cate_id];
                        if(!index && index!==0){
                            //to-do
                        }else{
                            clean_conditions[cate_index[cate_id]].selections.push(selections[i])
                        }
                    }
                    console.log(clean_conditions);
                    $scope.conditions = clean_conditions;
                });
            });
        });
    }

    $scope.$watch("token",renderHome);
    //Selection dialog
    function SelectionDialogCtrl($scope,$mdDialog,category,currentGrade,semester,student){
        var that = this;
        $scope.selectedCategory = category;
        currentGrade = currentGrade+"";
        this.loading = false;
        this.currentGrade = currentGrade;
        rest.courses_category.query({id:$scope.selectedCategory.id},function(courses){
            //to-do filter with grade
            var filteredCourses = [];
            for(var i=0;i<courses.length;i++){
                if(courses[i].grade == currentGrade){
                    filteredCourses.push(courses[i]);
                }
            }
            $scope.coursesTable = new NgTableParams({}, { dataset: filteredCourses});
            //console.log($scope.coursesTable);
        });
        this.selection = [];
        this.selectRow = function(item){
            item.selected = !item.selected;
        };
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
    $scope.courseSelection = [];
    $scope.openSelection = function(category,ev){
        //$scope.selectedCategory = category;
        var useFullScreen = ($mdMedia('sm') || $mdMedia('xs'))  && $scope.customFullscreen;
        $mdDialog.show({
          controller: SelectionDialogCtrl,
          controllerAs:"ctrl",
          templateUrl: 'templates/student/studentCourseSelectDialog.html?v='+Math.random(),
          parent: angular.element(document.body),
          targetEvent: ev,
          clickOutsideToClose:false,
          locals:{
              category:category,
              currentGrade:$scope.currentGrade,
              semester:$scope.semester,
              student:$scope.student,
          },
          fullscreen: useFullScreen
        })
        .then(function(selections) {
            renderHome();
            $scope.selectionCategory = {};
        },function(){
            $scope.selectionCategory = {};
        });
        $scope.$watch(function() {
            return $mdMedia('xs') || $mdMedia('sm');
        }, function(wantsFullScreen) {
            $scope.customFullscreen = (wantsFullScreen === true);
        });
    };
    $scope.removeSelection = function(selection){
        rest.course_selections.remove({},selection,function(){
            renderHome();
        },function(){
            alert("Remove failed!");
        });
    };
}]);
