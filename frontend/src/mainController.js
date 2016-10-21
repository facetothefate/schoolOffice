angular.module('school-office').controller('mainController',[
    'AuthService',
    '$rootScope',
    '$scope',
    '$mdDialog',
    '$mdMedia',
    function(AuthService,$rootScope, $scope, $mdDialog, $mdMedia){
        var useFullScreen = ($mdMedia('sm') || $mdMedia('xs'))  && $scope.customFullscreen;
        function LoginController($rootScope,$scope,$mdDialog){
            this.login = function(){
                $scope.error_msg = "";
                AuthService.login($scope.username,$scope.password).then(function(res){
                    $rootScope.$broadcast('login-success',res);
                    $mdDialog.cancel();
                },function(res){
                    $scope.error_msg = res.data.error;
                });
            };
        }
        function showDialog(){
            $mdDialog.show({
                controller: LoginController,
                controllerAs: 'ctrl',
                templateUrl: 'templates/login.html',
                parent: angular.element(document.body),
                clickOutsideToClose:false,
                fullscreen: useFullScreen
            });
        }
        var token = AuthService.getLocalToken();
        if(!token){
            showDialog();
        }else{
            $rootScope.token = token;
        };
        $scope.$on('login-success',function(){
            var token = AuthService.getLocalToken();
            if(!token){
                showDialog();
            }else{
                $rootScope.token = token;
            };
        })
        $scope.$on('access-denied',function(){
            showDialog();
        });
        $scope.logout = function(){
            AuthService.logout();
            $rootScope.$broadcast('logout');
            $rootScope.token = null;
            showDialog();
        };
    }
]);
