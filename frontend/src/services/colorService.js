angular.module('school-office').factory('ColorService',function($resource){
    /////////////////////////
    //Color array
    /////////////////////////
    var __light_colors=[
    	"mdl-color--red-50",
    	"mdl-color--pink-50",
    	"mdl-color--purple-50",
    	"mdl-color--deep-purple-50",
    	"mdl-color--indigo-50",
    	"mdl-color--blue-50",
    	"mdl-color--light-blue-50",
    	"mdl-color--cyan-50",
    	"mdl-color--teal-50",
    	"mdl-color--green-50",
    	"mdl-color--light-green-50",
    	"mdl-color--lime-50",
    	"mdl-color--yellow-50",
    	"mdl-color--amber-50",
    	"mdl-color--orange-50",
    	"mdl-color--deep-orange-50"
    ];
    return {
        getLightColor:function(flag){
            return __light_colors[flag%__light_colors.length];
        },
    };
});
