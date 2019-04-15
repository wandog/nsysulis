/**
 * @author jhma
 * @email  jhma@staff.nsysu.edu.tw
 * log:    2012/11/14 
 *              
 */

/*******************************************************
 * function:
 *      class of modscript which is used to create button of "mon to fri/weekend", "auto/manual" and "script name"
 * arguements for initializer:
 *      name:   used to show the text of this button
 *      myPoint:
 *      tarPoint:
 *      Ctop:
 *      Cleft:
 * methods:
 *      creBtn:
 *          arguemnts:
 *              state:  true or false of this button's data named "state"
 *              func:   the function will be executed when the button is clicked
 *              ele:    the "td" dom that we want to put the button into
 *              seq:    not used so far
 *      
 *******************************************************/
function ModScript(name,myPoint,tarPoint,Ctop,Cleft){
    this.dis=name||'';
    var self=this;
    self.creBtn=function(state,func,ele,seq){
        //create a div dom and set its css attribute 
        var btn=$('<div>').css({'background-color':'#F88017','width':'200','font-size':'25px','border':'4px solid #8A4117','text-align':'center'}).data('state',state).
        //register the func,put in data of button itself,that will be executed when button is pressed
        data('func',func).click(
        function(){
            $(this).data('func')(this);//"this" here mean the dom object itself
        }).corner('10px');
        $(btn).html("<center>"+this.dis+"</center>");//"this" here mean modescript's object
        
        ele.append($(btn));
                  
    };
}
/*****************************************************************
 * function:
 *      translate the string of time like 8:00 into 480 minutes
 * arguments:
 *      on_time_s:the string of on time
 *      off_time_s:the string of off time
 * return:
 *      JSON includes the time "on" and time "off"
 ******************************************************************/  
function S2m(on_time_s,off_time_s){
    var time_on_p=(on_time_s).split(/[\:]+/);
    var time_off_p=(off_time_s).split(/[\:]+/);
    time_on_p=Math.floor((Number(time_on_p[0])*60+Number(time_on_p[1])));
    time_off_p=Math.floor((Number(time_off_p[0])*60+Number(time_off_p[1])));
    
    //console.info({'on':time_on_p,'off':time_off_p});
    return {'on':time_on_p,'off':time_off_p};
        
}        

/***************************************************************
 *function:
 *      adjust the time period of time bar
 *arguements:
 *      ele: 
 *          the dom has the data of time_on and time_on_S, 
 *          in fact it just is div off of time bar
 *      timeOn: 
 *          the time in type like 8:00
 *      timeOff: 
 *          the time in type like 23:00      
 * return:
 *      none
 * **************************************************************/
     
function adjBar(ele,timeOn,timeOff,ele2){
    /* the dom of input of this dialog
    <input type="text" value="enter start time" id="staTiIn">
    <input type="text" value="enter end time" id="etaTiIn">
    */
    //timeObj is an object of time in type of minutes
    var timeObj=S2m(timeOn,timeOff);
    if(ele!=null){
        var ObjDomOn=$('#'+$(ele).data('control_node_on_id'));
        var ObjDomOff=$('#'+$(ele).data('control_node_on_id')).parent();//this part could be modified
    }else{
        var ObjDomOn=ele2;
        var ObjDomOff=ele2.parent();
    }
    var widthOff=Number(ObjDomOff.css('width').replace('px',''));           
    var startPot=widthOff*(Number(timeObj.on))/1440;
    var lenOn=widthOff*(Number(timeObj.off)-Number(timeObj.on))/1440;
    //console.info(startPot+' '+lenOn);
    //$(div_on).position({my:"left center",at:"left center", of:$(div_off) ,offset:"+"+start_point+" 0",collision:"fit"}).css('z-index','1');
    
    ObjDomOff.data('on_time',{'on':timeObj.on,'off':timeObj.off});//<---this part should be modified!!!
    ObjDomOff.data('on_time_S',{'on':timeOn,'off':timeOff});
    ObjDomOn.position({my:"left center",at:"left center", of:ObjDomOff ,offset:"+"+startPot+" 0",collision:"fit"});
    ObjDomOn.css('width',lenOn);
}        



        
/***************************************************
 * function:
 *      class of the indicator of specified controlpoint or node
 * 
 * arguments for initializer:
 *      direction: east or west of this node
 *      floor: 3~10 floor of this node
 *      func: air or light of this node
 *      side: side of kernel of this node
 * methods:
 *      createBtn: 
 *          create the bottom of this indicator to show the name of this node 
 *          and the status of "on" and "off" by color
 *      
 ***************************************************/        
        
function statusIndi(direction,floor,func,side,other){
    this.direction=direction||'';
    this.floor=floor||'';
    this.func=func||'';
    if(other){
        this.side=side+"_"+other||'';
    }else{
        
        this.side=side||'';    
    }
    
    if(this.side==''){
        this.buttonName=this.direction+'_'+this.floor+'_'+this.func;
    }else{
        this.buttonName=this.direction+'_'+this.floor+'_'+this.func+'_'+this.side;    
    }
    
    var self=this;
    indicator=this;
    
    self.createBtn=function(ele){
        //use self here will cause problem, self doesn't point to the dom it self but point the object when
        //this method is activated, the trblock object
        // console.info(indicator);
        
        var div_status=$('<div>').attr({'id':'div_'+indicator.buttonName}).html(indicator.buttonName).corner('5px').css({'width':'170','height':'30','background-color':'#FF00FF'});
        
        $(div_status).data('status',true).click(function(){
            if($(indicator).data('status')==true)
            {   $(indicator).css({'background-color':'#00FF00'});
                $(indicator).data('status',false);
            }else{
                $(indicator).css({'background-color':'#FF00FF'});
                $(indicator).data('status',true);
            }
        });
        
        ele.append($(div_status));
        
    };
    
  
}



/*
var test_status=new statusIndi();
test_status.showSelf();

test_status.GetSta('east','10','light','side');
test_status.showSelf();
*/
/*************************************************************
 * function:
 *          class ot timebar
 * arguments for initializer:
 *          cp:
 *          bar_name:
 *          on_start:
 *          on_off:
 *          timeStringO:
 * methods:
 *          
 *************************************************************/

function timebar(cp,bar_name,on_start,on_off,timeStringO){
    //ele is used to judge which element to attain
    var self=this;
    
    if(on_start>on_off){
        on_off=on_start;
    }
    
    this.bar_name=bar_name||'none';
    this.on_start=on_start||'1000'; //adjust the offset in position of table_off
    this.on_off=on_off||'1000';     //adjust the width of table_on
    this.timeStringO=timeStringO||{'on':'8:00','off':'22:00'};
    this.cp=cp;
    //this.baseL=baseL||100;
    //this.bar_size=bar_size;
    //this.position=position;
    
    self.timeRange=function(){  //this is to setup the action when dbclick event happen on the timebar of 'off' dom
        $('#div_off_'+this.bar_name).dblclick(function(){
            var div_on_id=$(this).attr('id').replace('off','on');
                                //save the id of on bar in the data of time input dialog
            //console.info(div_on_id);
            $("#TimeInput").data('control_node_on_id',div_on_id).dialog('open');
        });
    };

    
    self.create=function(ele,width_bar){//input element is the target for attain
        //bar of off
        var div_off=$('<div>').attr({'id':'div_off_'+this.bar_name}).html('off').css({'text-align':'left','width':width_bar,'height':'30','background-color':'#ff0000','z-index':'2'});
        //var td_off=$('<td>').html('off');
        //$(table_off).append(td_off).data('on_time',{'on':this.on_start,'off':this.on_off});
        $(div_off).data('on_time',{'on':this.on_start,'off':this.on_off}).data('on_time_S',this.timeStringO);
        $(div_off).data('node',this.cp);
        //$(table_off)              
        ele.append(div_off);
        
        //parameter to control the width and position of bar of table_on
        var start_point=($(div_off).width())*((this.on_start)/1440);
        var end_point=(($(div_off).width())*((this.on_off)/1440));
        var lenTaOn=end_point-start_point;
        //alert(lenTaOn);
        //bar of on
        var div_on=$('<div>').attr({'id':'div_on_'+this.bar_name}).html('on').css({'width':lenTaOn,'height':'26','background-color':'#00FFFF'});
        //var td_on=$('<td>');//.html('on');
        //$(table_on).append(td_on);
        $(div_off).append(div_on);
        //for delay
        for (i=0;i<10000;i++){
            for (j=0;j<1;j++){
            	
            }
        }
        
        //control the position of div_on
        $(div_on).position({my:"left center",at:"left center", of:$(div_off) ,offset:"+"+start_point+" 0",collision:"none"}).css('z-index','1');
        
        //alert("#table_off_"+this.bar_name);
    };
    
    
        
}

/*************************************************
 * function:
 *      class of total bar block
 * arguments for initializer:
 *      controlpoint:
 *      bar_name:
 *      time_on:
 *      time_off:
 *      timeStringO:
 * 
 * methods:
 *      creBlock:
 *          gen the timebar block 
 * 
 *      
 *************************************************/


function TrBlock(controlpoint,bar_name,time_on,time_off,timeStringO){
    
    this.barNameA=bar_name.split(/[\_]+/);
    this.timeSTRING='';
    //console.info(this.barNameA);
    
    this.timebar_block=new timebar(controlpoint,bar_name,time_on,time_off,timeStringO);
    
    if(this.barNameA[4]){
        // console.info(this.barNameA[4]);
        this.StaIndi_block=new statusIndi(this.barNameA[0],this.barNameA[1],this.barNameA[2],this.barNameA[3],this.barNameA[4]);
    }else{
        if(this.barNameA[3]){
            this.StaIndi_block=new statusIndi(this.barNameA[0],this.barNameA[1],this.barNameA[2],this.barNameA[3]);    
        }else{
            this.StaIndi_block=new statusIndi(this.barNameA[0],this.barNameA[1],this.barNameA[2]);    
        }
        
    }
    
    
    
    var self=this;  //pay attention the reason why this line is put here but the start of this class
                //once it is put before the setup of attribute, what self point to may be changed when the attribute is a object(by object's constructor) 
    self.creBlock=function(ele){
        
        var tr=$('<tr>');
        var td_1=$('<td>');
        
        var td_2=$('<td>').attr({'width':300}).mouseenter(function(){
            $('td[name=timeONrange]').trigger('showON');
            // var timeSTRING=$(this).children().data('on_time_S').on+'~'+$(this).children().data('on_time_S').off;
            // var timeSHOWtd=$(this).next();
            // timeSHOWtd.html(timeSTRING).css({'background-color':'#ff0000','color':'#00ffff'});
            // $('#plaShowTime').html(timeSTRING);      
           
            // $("#TimeRan").dialog('open');
            })
            .mouseout(function(){$("#TimeRan").dialog('close');});
        //console.info(self.timebar_block);
        var btn=$('<button>').text('switch');
            
        var td_3=$('<td>').append(btn);//switch button for the node in same tr block
        
        // console.info('hi');
        // console.info($(this));
        var td_4=$('<td>').attr({'name':'timeONrange'}).bind('showON',function(){
        		var div_off=$(this).prev().children();
        		$(this).html(div_off.data('on_time_S').on+'~'+div_off.data('on_time_S').off)
        		.css({'background-color':'#ff0000','color':'#00ffff'});	
        	});                                                                   
        
        $(tr).append(td_3).append(td_1.attr({'align':'center'})).append(td_2).append(td_4);
        ele.append(tr);
        self.timebar_block.create($(td_2),200);//pay attention the reason why this line is put here (for the right relative position of bar)
        self.timebar_block.timeRange();
        
        self.StaIndi_block.createBtn($(td_1));
        
        pollingSTATUS(td_1.children(),td_2.children().data('node'),3000)
        btn.click(function(){
            // console.info(this);
			nodename=td_2.children().data('node')
            // console.info();
            gg={};gg['fn']='operationMANUAL';gg['node']=nodename;
            $.post('sqlite_test.php',gg,function(d){
                // console.info(d);
                if (d['mode']=='auto'){
                    alert('in auto mode, you can\'t change the state of controlpoint!');
                    return;
                }else{//the mode is 'manual'
                    // console.info(this);
                    // console.info(nodename);
                }    
            },'json');
        });
    };

}

/**********************************************************************************************
 * function:
 *      polling the status of relay in a specified period for specified status indicator of timebar
 * arguemments:
 *      period:     unit in msec
 *      tar:        the dom's css background-color that we want to show the state of realy
 *      elefrom:    the dom to get the control node's id, ex a,b or c
 * reruen:
 *      none
 ***********************************************************************************************/
var pollingSTATUS=function(tar,elefrom,period){
    setInterval(function(){
        gg={};
        gg['fn']='Pooling';
        gg['node']=elefrom
        $.post('sqlite_test.php',gg,function(d){
            //console.info(d)
            //console.info(d['itis']);
            if (d['itis']==1){		//present the state turn on
                tar.css({'background-color':'#FF00FF'});
            }else{
				if(d['itis']==-1){	//present the state abnormal
					tar.css({'background-color':'#842DCE'});
				}else{				//persent the state turn off
					tar.css({'background-color':'#00FF00'});
				}
                
            }
        },'json')
        //console.info(tar)
    },period);
     
}



