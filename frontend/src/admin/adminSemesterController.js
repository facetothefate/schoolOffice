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

angular.module('school-office').controller('AdminSemesterScheduleController',
[
  "NgTableParams",
  "$scope",
  "$routeParams",
  "$location",
  "RestService",
function(NgTableParams,$scope,$routeParams,$location,rest){
    //Init
    $scope.unscheduled = [];
    $scope.scheduled=[[],[],[],[],[]];
    $scope.weekday=[
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday"
    ];
    function getTime(scheduled){
        for(var i =0;i<scheduled.length;i++){
            var start_time = new Date("1991-02-13 "+scheduled[i].start_time);
            var end_time = new Date("1991-02-13 "+scheduled[i].end_time);
            scheduled[i].start_hour = start_time.getHours();
            scheduled[i].start_minute = start_time.getMinutes();
            scheduled[i].end_hour = end_time.getHours();
            scheduled[i].end_minute = end_time.getMinutes();
        }
    }
    function formatTime(hour,miniute){
        return (100+hour+'').substr(1) + ':' + (100+miniute+'').substr(1) + ':00';
    }
    function render(){
        if($routeParams.semester){
            rest.course_selections.semester_unscheduled_summary({semester:$routeParams.semester},function(summary){
                for(var i=0;i<summary.length;i++){
                    summary[i].new = true;
                }
                $scope.unscheduled = summary;
            });
            rest.courses_schedule.get_by_weekday({semester:$routeParams.semester,repeat_day:0},function(scheduled){
                getTime(scheduled);
                $scope.scheduled[0]=scheduled;
            });
            rest.courses_schedule.get_by_weekday({semester:$routeParams.semester,repeat_day:1},function(scheduled){
                getTime(scheduled);
                $scope.scheduled[1]=scheduled;
            });
            rest.courses_schedule.get_by_weekday({semester:$routeParams.semester,repeat_day:2},function(scheduled){
                getTime(scheduled);
                $scope.scheduled[2]=scheduled;
            });
            rest.courses_schedule.get_by_weekday({semester:$routeParams.semester,repeat_day:3},function(scheduled){
                getTime(scheduled);
                $scope.scheduled[3]=scheduled;
            });
            rest.courses_schedule.get_by_weekday({semester:$routeParams.semester,repeat_day:4},function(scheduled){
                getTime(scheduled);
                $scope.scheduled[4]=scheduled;
            });
        }else{
            $location.path('../');
        }
    }
    $scope.save = function(){
        var data = [];
        for(var i=0;i<$scope.scheduled.length;i++){
            for(var j=0; j<$scope.scheduled[i].length;j++){
                if($scope.scheduled[i][j].new){
                    $scope.scheduled[i][j].repeat_day = i;
                    $scope.scheduled[i][j].start_time = formatTime($scope.scheduled[i][j].start_hour,$scope.scheduled[i][j].start_minute);
                    $scope.scheduled[i][j].end_time = formatTime($scope.scheduled[i][j].end_hour,$scope.scheduled[i][j].end_minute);
                    $scope.scheduled[i][j].code = $scope.scheduled[i][j].code.trim();
                    data.push($scope.scheduled[i][j]);
                }
            }
        }
        if(data.length){
            $scope.loading = true;
            rest.courses_schedule.save({semester:$routeParams.semester},data,function(){
                $scope.loading = false;
                render();
            });
        }
    };
    $scope.cancel = function(list,item,index){
        if(item.new){
            list.splice(index, 1);
            $scope.unscheduled.push(item);
        }
    };
    render();
}]);
