angular.module('school-office').controller('StudentHomeController',
[
  "$scope",
  "RestService",
  "$mdMedia",
  "$mdDialog",
  "NgTableParams",
function($scope,rest,$mdMedia,$mdDialog,NgTableParams){
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

    rest.student_username.get($scope.token,function(student){
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

        //then get all the conditons
        rest.conditions.get({diploma:1},function(conditions){
            var clean_conditions = [];
            var cate_index = {};
            var index;
            for(var i = 0;i<conditions.length;i++){
                var cate_id = conditions[i].so_course_categories_id;
                if(!cate_index[cate_id]){
                    clean_conditions.push({
                        details:[getCondtionText(conditions[i])],
                        conditions:[conditions[i]],
                        category:{
                            title:conditions[i].title,
                            id:cate_id,
                        }
                    });
                    index = clean_conditions.length-1;
                    cate_index[cate_id] = index;
                }else{
                    var condition = clean_conditions[cate_index[cate_id]];
                    condition.details.push(getCondtionText[i]);
                    condition.conditions.push(condition[i]);
                }
            }
            console.log(clean_conditions);
            $scope.conditions = clean_conditions;
        });
    });
    //Selection dialog
    function SelectionDialogCtrl($scope,$mdDialog,category,currentGrade){
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
        this.select = function(){
            //$scope.coursesTable
            for(var i=0; i<$scope.coursesTable.data.length;i++){
                if($scope.coursesTable.data[i].selected){
                    delete $scope.coursesTable.data[i].selected;
                    this.selection.push($scope.coursesTable.data[i]);
                }
            }
            this.loading = true;
            /*$mdDialog.hide({
                category:category,
                selection:this.selection
            });*/
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
              currentGrade:$scope.currentGrade
          },
          fullscreen: useFullScreen
        })
        .then(function(selections) {
            /*for(var i=0;i<selections;i++){
                $scope.courseSelection.push(selections[i]);
            }*/
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
}]);
