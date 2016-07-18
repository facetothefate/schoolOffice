/////////////////////////
//Dependencies
/////////////////////////
var schooloffice = angular.module('school-office', 
	[
		'ngMaterial',
		'ngRoute'
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
    /*.when('/login', {
      controller:'UserController',
      templateUrl:'templates/login.html',
    })
    .otherwise({
      redirectTo:'/login'
    })*/
    .otherwise({
      redirectTo:'/adminHome'
    });
});