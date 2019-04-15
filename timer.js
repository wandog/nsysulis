/**
 * @author jhma
 */
var Appendzero=function(arg){
    if(arg<10)
        return '0'+arg;
    else
        return arg;
};
var presentTime=function(){
    var date=new Date();
    var m=date.getMinutes();
    var h=date.getHours();
    var s=date.getSeconds();
    
    return '現在時間:\n'+Appendzero(h)+':'+Appendzero(m)+':'+Appendzero(s);
};

