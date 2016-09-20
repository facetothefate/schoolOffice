angular.module('school-office').controller('StudentScheduleController',
[
  "$scope",
  "RestService",
function($scope,rest){
    $scope.scheduled=[[],[],[],[],[]];
    $scope.weekday=[
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday"
    ];
    rest.semesters.query(function(semesters){
        $scope.semesters = semesters;
        $scope.selected_semester = semesters[0];
    });
    $scope.$watch('selected_semester',function(newValue,oldValue){
        rest.courses_schedule.get_by_weekday({semester:$scope.selected_semester.id,repeat_day:0},function(scheduled){
            $scope.scheduled[0]=scheduled;
        });
        rest.courses_schedule.get_by_weekday({semester:$scope.selected_semester.id,repeat_day:1},function(scheduled){
            $scope.scheduled[1]=scheduled;
        });
        rest.courses_schedule.get_by_weekday({semester:$scope.selected_semester.id,repeat_day:2},function(scheduled){
            $scope.scheduled[2]=scheduled;
        });
        rest.courses_schedule.get_by_weekday({semester:$scope.selected_semester.id,repeat_day:3},function(scheduled){
            $scope.scheduled[3]=scheduled;
        });
        rest.courses_schedule.get_by_weekday({semester:$scope.selected_semester.id,repeat_day:4},function(scheduled){
            $scope.scheduled[4]=scheduled;
        });
    })
}]);
