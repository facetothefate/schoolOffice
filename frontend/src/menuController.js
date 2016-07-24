var menuConfig = {
	"admin":[
		{
			title:"Home",
			icon:"home",
			href:"#/adminHome",
		},
		{
			title:"Semesters",
			icon:"schedule",
			href:"#/adminSemeters",
		},
		{
			title:"Courses",
			icon:"library_books",
			href:"",
		},
		{
			title:"Students",
			icon:"face",
			href:"#/adminStudents",
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
	"teacher":[],
	"student":[]
}

angular.module('school-office').controller('MenuController',function($scope,$location){
	$scope.$on("login-success",function(data){
		$scope.menu = menuConfig[data['role']];
		$scope.user = data['username'];
	});
	//change it later
	$scope.menu = menuConfig['admin'];
});
