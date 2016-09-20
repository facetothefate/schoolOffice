angular.module('school-office').factory('DateService',function(){
    return{
        'YYYY-MM-DD':function(date){
            return date.getFullYear()+'-'+date.getMonth()+'-'+date.getDate();
        },
        'HH:MM:00':function(date){
            return (100+date.getHours()+'').substr(1) + ':' + (100+date.getMonth()+'').substr(1) + ':00';
        }
    }
});
