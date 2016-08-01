/////////////////////////
//Dependencies
/////////////////////////
var schooloffice = angular.module('school-office',
	[
		'ngMaterial',
		'ngRoute',
		"ngTable",
		"ngResource"
	]);


////////////////////////
//Route configuration
////////////////////////
schooloffice.config(function($routeProvider) {

  $routeProvider
    .when('/adminHome', {
      controller:'AdminHomeController',
      templateUrl:'templates/admin/adminHome.html',
    })
	.when('/adminStudents', {
      controller:'AdminStudentController',
      templateUrl:'templates/admin/adminStudent.html',
    })
	.when('/adminCourses', {
      controller:'AdminCourseController',
      templateUrl:'templates/admin/adminCourse.html',
    })
    .when('/adminStudent/:action/:studentNumber', {
      controller:'AdminStudentDetailController',
      templateUrl:'templates/admin/adminStudentDetail.html',
    })
    /*.when('/login', {
      controller:'UserController',
      templateUrl:'templates/login.html',
    })

    .otherwise({
      redirectTo:'/login'
    })*/
    .otherwise({
      redirectTo:'/'
    });
});
///////////////////////////
//Http config
//////////////////////////
schooloffice.config(function($httpProvider) {
	$httpProvider.interceptors.push('AuthHttpInterceptor');
});
