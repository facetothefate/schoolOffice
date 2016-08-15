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
  	//admin
    .when('/adminHome', {
      controller:'AdminHomeController',
      templateUrl:'templates/admin/adminHome.html',
    })
	.when('/adminStudents', {
      controller:'AdminStudentController',
      templateUrl:'templates/admin/adminStudent.html',
    })
	.when('/adminStudent/:action/:studentNumber', {
      controller:'AdminStudentDetailController',
      templateUrl:'templates/admin/adminStudentDetail.html',
    })
	.when('/adminCourses', {
      controller:'AdminCourseController',
      templateUrl:'templates/admin/adminCourse.html',
    })
	.when('/adminSemesters', {
      controller:'AdminSemesterController',
      templateUrl:'templates/admin/adminSemester.html',
    })

	//Studnets
	.when('/studentHome', {
      controller:'StudentHomeController',
      templateUrl:'templates/student/studentHome.html',
    })

	//teachers
	//system
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
