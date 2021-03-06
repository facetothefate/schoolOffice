angular.module('school-office').factory('AuthService',function($http,$q,$window){
    return {
        'login':function(username,password){
            var deferred = $q.defer();
            $http.post("../api/user/login", {
                username: username,
                password: password
            }).then(function(result) {
                $window.sessionStorage.setItem('0213school-office-token',JSON.stringify(result.data));
                deferred.resolve(result.data);
            }, function(error) {
                deferred.reject(error);
            });
            return deferred.promise;
        },
        'logout':function(username){
            $window.sessionStorage.removeItem('0213school-office-token');
        },
        'getLocalToken':function(){
            return JSON.parse($window.sessionStorage.getItem('0213school-office-token'));
        }
    };
});
angular.module('school-office').factory('AuthHttpInterceptor',function($q,$injector,$window){
    return {
        'request':function(req){
            var token = JSON.parse($window.sessionStorage.getItem('0213school-office-token'));
            if(token){
                req.headers['Token-Authorization-X'] = token.token;
            }
            return req;
        },
        'responseError': function(res){
            if(res.status == 401){
                var rootScope = $injector.get('$rootScope');
                rootScope.$broadcast('access-denied');
                return $q.reject(res);
            }
            return $q.reject(res);
        }
    };
});
