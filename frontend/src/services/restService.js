angular.module('school-office').factory('RestService',function($resource){
    return {
        'students':$resource('../api/students/:id',{id:'@id'}),
        'courses':$resource('../api/courses/:id',{id:'@id'}),
    };
});
