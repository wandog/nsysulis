/**
 * @author jhma
 */

/*************************************************************************
 function: 
    get the data in table of selected back to help    
 arguments:
    div_scriptName: the dom object of the div to display present script
return:
    none    
****************************************************************************/
    

var iniScriptData=function(div_scriptName){
    gg={};gg['fn']='GetSSData';
    $.post('sqlite_test.php',gg,function(d){
        //init the script selected's name
        div_scriptName.html(d.scriptInitial);    
        
        
        //console.info(d.TTinital);
        //init the div and button of timeRange
        if(d.TTinital=='normal'){
            //don't do anything; the defalut is mon to fri
        }else{
            // if(d.TTinital=='weekend'){
            changeT($('#timeRange_select div'),true);//toggle this div from 'mon to fri' to 'weekend'
            // }
        }
        if(d.modeInitial=="manual"){
             // alert('hi');
            // console.info($('#mod_select div'));
            changeM($('#mod_select div'));
        }
        
        // getScriptBackO('floor','direction','function');
        getScriptBackO('direction','side','floor');
    },'json');      
}





