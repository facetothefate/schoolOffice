var menuConfig = {
	3:[
		{
			title:"Home",
			icon:"home",
			href:"/adminHome",
		},
		{
			title:"Semesters",
			icon:"schedule",
			href:"/adminSemeters",
		},
		{
			title:"Courses",
			icon:"library_books",
			href:"/adminCourses",
		},
		{
			title:"Students",
			icon:"face",
			href:"/adminStudents",
		},
		{
			title:"Teachers",
			icon:"school",
			href:"",
		},
		{
			title:"Transcripts",
			icon:"done",
			href:"",
		},
	],
	2:[],
	1:[]
}

angular.module('school-office').controller('MenuController',function($scope,$location){
	if($scope.token){
		$scope.menu = menuConfig[$scope.token['role']];
		$scope.user = $scope.token['username'];
		var path = $location.path();
		if(path==="/"){
			 $location.path(menuConfig[$scope.token['role']][0].href);
		}
	}
	$scope.$on("login-success",function(e,token){
		$scope.menu = menuConfig[token['role']];
		$scope.user = token['username'];
		$scope.token = token;
	});
});
