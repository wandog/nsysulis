/********************************************************
 * function:
 *      check if the order deamnd is setup
 * argument:
 *      the text value of options of order demand
 * return
 *      boolean:if the order demand is set 
 ********************************************************/
var optionSTATEtest=function(sel_val1,sel_val2,sel_val3){
    var order_seq={
        '1' : sel_val1,
        '2' : sel_val2,
        '3' : sel_val3
    };
    
    for(var i in order_seq){
        //console.info(order_seq[i]);
        if((order_seq[i]!='floor')&&(order_seq[i]!='function')&&(order_seq[i]!='side')&&(order_seq[i]!='direction')){
            alert('you not yet assign the order of demand!!');
            return false;
        }
    }
    return true;
}


 /****************************************************************
  * function:
  *     used to gen the all timebar
  *         getScriptBackO->generateR->getAllBar 
  *                                     i
  *                         this function create timebar needed
  *arguments:
  *     sel_val1,2,3: the text value of order demand optiopns
  * return:
  *     none 
  ****************************************************************/

var getScriptBackO=function(sel_val1,sel_val2,sel_val3){
    //console.info(sel_val1+sel_val2+sel_val3);
     
    
    
    var order_seq={
        '1' : sel_val1,
        '2' : sel_val2,
        '3' : sel_val3
    };
    
    // for(var i in order_seq){
        // //console.info(order_seq[i]);
        // if((order_seq[i]!='floor')&&(order_seq[i]!='side')&&(order_seq[i]!='function')&&(order_seq[i]!='direction')){
            // alert('you not yet assign the order of demand!!');
            // return false;
        // }
    // }
    gg={};gg['fn']='getTType';
    $.post('sqlite_test.php',gg,function(d){
       
        //self.getBarAS(d.script,d.time_type);
        gg={};gg['order']=order_seq;
        gg['fn']='FBbyOrder';
        gg['script']=d.script;
        gg['time_type']=d.time_type;
        
        $.post('sqlite_test.php',gg,function(d){
           //console.info(d);
           generateR(d);
        },'json');
        
    },'json');
    return true    
}


/***********************************************************************************
 *function:
 *      control the bars's gen according to the feedback of FBbyOrder, a case in 
 *      sqlite_test.php, which will return the node data according the order demand
 *      from options of "direction", "side", "floor" and "function"
 *arguments:
 *      JOSN of data got from sqlite_test.php
 * return:
 *      none
 ***********************************************************************************/
var generateR=function(d){
	
	//if there is old bar, then remove them at first
	if($('#test_2').children()){
		
		var tr=$('<tr>');
		//tr.append(th.html('開關')).append(th.html('指示燈')).append(th.html('時間軸')).append(th.html('啟動時間'));
        tr.append($('<th>').html('開關')).append($('<th>').html('指示燈(開/關=粉紅/綠色)'))
        .append($('<th>').html('時間軸(00:00~24:00)')).append($('<th>').html('啟動時間'));
        $('#test_2').children().remove();
        $('#test_2').append(tr);
    	
    }	
	
	
	$.each(d,function(key,value){ 
	    var name_bar=value.bar_name;
	    var timeHandle=new getAllBar(value.controlpoint,name_bar,value.on_time,value.off_time);
	    timeHandle.getBarAS();
	})
    
};


/****************************************************************
 * function:
 *      class that
 *      use to extract the time from php's feedback bar data'      
 *      use the function adjBar in barObj.js to put the time period into timebar      
 *       
 * arguments for initializer:
 *      
 * methods:
 *      getBarAS:
 *          arguments:
 * 
 *          return:
 * 
 * 
 ****************************************************************/

var getAllBar=function(controlpoint,name_bar,on_time,off_time){
	var self=this;
	this.name_bar=name_bar;
	this.on_time=on_time||'5:00';
	this.off_time=off_time||'22:00';
	
	self.getBarAS=function(){
		
		var time_on_p=(this.on_time).split(/[\:]+/);
		var time_off_p=(this.off_time).split(/[\:]+/);
		time_on_p=Math.floor((Number(time_on_p[0])*60+Number(time_on_p[1])));
		time_off_p=Math.floor((Number(time_off_p[0])*60+Number(time_off_p[1])));
		
		//console.info(time_on_p+' '+time_off_p);
		//time with p is time in minutes; this.on_time and this.off_time is time in string like 8:21
		//console.info(this.name_bar); 
		var line_1=new TrBlock(controlpoint,this.name_bar,time_on_p,time_off_p,{'on':this.on_time,'off':this.off_time});
		line_1.creBlock($('#test_2'));
		
	};
}; 