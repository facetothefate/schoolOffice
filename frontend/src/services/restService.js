angular.module('school-office').factory('RestService',function($resource){
    return {
        'students':$resource('../api/students/:id',{id:'@id'}),
        'student_username':$resource('../api/students/user/:username',{username:"@username"}),
        'courses':$resource('../api/courses/:id',{id:'@id'}),
        'courses_category':$resource('../api/courses/category/:id',{id:'@id'}),
        'semesters':$resource('../api/semesters/:id',{id:'@id'}),
        'conditions':$resource('../api/conditions/:diploma',{diploma:'@so_diplomas_id'},{
            'get':{
                url:"../api/conditions/:diploma",
                isArray:true,
                method:"GET",
            }
        })
    };
});
