<?php
session_start();
if($_SESSION['login'])  {
        
    if($_SESSION['login']!=true){
        header("location:index.php")
        or die("you are not permitted to access this page!");    
    }
}else{  //means that session['login] is not created!
    header("location:index.php")
    or die("you are not permitted to access this page!");
}

?>

<!DOCTYPE HTML PUBLIC “-//W3C//DTD HTML 4.01 Transitional//EN" “http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>
<LINK REL="SHORTCUT ICON" HREF="ICO.ico">
<title>control_panel_demo</title>
<style type="text/css">
	.tableB{
	   background:-moz-linear-gradient(22% 43% 0deg, #C53436, #E44E28 10%);   
	}
	.btn1{
	   background-color:#00FFFF;
	   font-size:20px;
	   border:3px solid #3B9C9C;
	   width:100px;
	   text-align:center;   
	}
	.btn1over{
	   background-color:#43C6DB;   
	}
	.btnDOWN{
	    border:3px solid #736AFF;
	}
	
	.textINPUT{
/*    	    background-color:#43C6DB;*/
		width:100px;
	}
	
</style> 
<!-- 	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" /> -->
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
<link type="text/css" href="css/sunny/jquery-ui-1.8.24.custom.css" rel="stylesheet" />
<script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.24.custom.min.js"></script>
<!-- External / vendor scripts -->

<script type="text/javascript" src="js/jquery.corner.js"></script>
<script type="text/javascript" src="reportG.js"></script>
<script type="text/javascript" src="orderOptionCrl.js"></script>
<script type="text/javascript" src="barObj.js"></script>
<script type="text/javascript" src="initializer.js"></script>
<script type="text/javascript" src="scriptIO.js"></script>
<script type="text/javascript" src="timer.js"></script>
<script type="text/javascript" src="batchSet.js"></script>
<!-- <script type="text/javascript" src="reflashPreScri.js"></script> -->
<script type="text/javascript">
	
	
		
//below is function we want to execute when btn generated by modscript is dblclick
/*******************************************************************
 * function:
 *     open the dialog for change the script
 * arguments:
 *     the dom of button "generate"
 * return:
 *     none
 *******************************************************************/
var changeScript=function(BtnEle){
//BtnEle is the ref of dom of button
    //console.info(BtnEle);
    gg={};gg['fn']='getScript';
    $.post('sqlite_test.php',gg,function(d){
        
        if($('#sel_script').children()){    
            $('#sel_script').children().remove();
            for (var i in d){
                opt=$('<option>').html(d[i]);
                $('#sel_script').append($(opt));
            }   
        }
    },'json');

    $('#ChangeScript').dialog('open');
};


/********************************************************************
 * function:
 *      to change the mode of table selected
 * arguements:
 *      the button of mode change of HMI     
 ********************************************************************/
var changeM=function(BtnEle){
//BtnEle is the ref of dom of button
    if($(BtnEle).data('state')==true){//means orginal state is 'auto'
        
        gg={};gg['fn']='BtnHandle';gg['button']='mode';gg['state']='manual';
        $.post('sqlite_test.php',gg,function(d){
            if(d.valueCheck=='manual'){
                $(BtnEle).html('手動模式');
                $(BtnEle).data('state',false).css({"background-color":"#57E964"});    
            }else{
                alert('mode switch error');
            }
                
        },'json');
        
        //var preColor=$(BtnEle).css('background-color');
        
    }else{
        gg={};gg['fn']='BtnHandle';gg['button']='mode';gg['state']='auto';
        $.post('sqlite_test.php',gg,function(d){
            if(d.valueCheck=='auto'){
                $(BtnEle).html('自動模式');
                $(BtnEle).data('state',true).css({"background-color":"#F88017"});
            }else{
                alert('mode switch error');
            }
                
        },'json');
        
    }
};


/******************************************************************
 * function:
 *      change time_type
 * arguments:
 *      BtnEle:
 *          the dom of button itsetf
 *      flagIni:
 *          to judge if the block of timebar generaton should be actived
 *          because once the selected mode is weekend when the HMI is initialing
 *          the function for initialize, iniScriptData, will pust the button of change time_type
 *          and this will cause error because the order demand is not selected when initializing!!    
 ******************************************************************/
var changeT=function(BtnEle,flagIni){//this function has toggle effect
                    //BtnEle is the ref of dom of button
    if(!flagIni){//when hmi is just initialized, ignore below test
        //test if the order demand is assigned
        flag=true;
        // flag=optionSTATEtest($('#sel_1 :selected').text(),$('#sel_2 :selected').text(),$('#sel_3 :selected').text())
        // if(!flag)
            // return; //if the order demand is not selected, 
                    //below operation will be ignored~
    }
    if($(BtnEle).data('state')==true){  //if true change to false; a toogle action
        
        gg={};gg['fn']='BtnHandle';gg['button']='timeRange';gg['state']='weekend';
        $.post('sqlite_test.php',gg,function(d){
            if(d.valueCheck=='weekend'){
                $(BtnEle).html('周六日');
                $(BtnEle).data('state',false).css({"background-color":"#57E964"});
                //below is to avoid the unset value of options in initial of HMI
                if(flagIni){
                    getScriptBackO('direction','side','floor');
                }else{
                    //get the timebar of weekend
                    //getScriptBackO<---this function is defined in reportG.js 
                    // getScriptBackO($('#sel_1 :selected').text(),$('#sel_2 :selected').text(),$('#sel_3 :selected').text());    
                	getScriptBackO('direction','side','floor');
                }
                    
            }else{
                alert('timerange switch error');
            }
        },'json');
        
        //var preColor=$(BtnEle).css('background-color');
        
    }else{                          //if false change to true; a toogle action
        gg={};gg['fn']='BtnHandle';gg['button']='timeRange';gg['state']='normal';
        $.post('sqlite_test.php',gg,function(d){
            if(d.valueCheck=='normal'){
                $(BtnEle).html('平日');
                $(BtnEle).data('state',true).css({"background-color":"#F88017"});
                //below is to avoid the unset value of options in initial of HMI
                if(flagIni){
                    getScriptBackO('direction','side','floor');
                }else{
                    //get the time bar data of mon to fri back
                    //getScriptBackO<---this function is defined in reportG.js
                    // getScriptBackO($('#sel_1 :selected').text(),$('#sel_2 :selected').text(),$('#sel_3 :selected').text());    
                	getScriptBackO('direction','side','floor');
                }
                      
            }else{
                alert('timerange switch error');
            }
        },'json');   
    }
    // console.info(BtnEle);
    
};	
	

	
	
$(document).ready(function(){
    
    //setup timer reflash interval      
    iniScriptData($('#selected_script div'));
    setInterval(function(){
        $('#presentTime').html(presentTime());
        
    },1000);
	
	
	//initial the dialog for showing time range
	$("#TimeInput").dialog({
    title:'Time Range Input',autoOpen: false,dialogClass:'vboxDialogContent',
    buttons:[{
    	text:'set',
    	click:function(){
    	    // regulatSTATUS=false;
    	    timeS=$('#staTiIn').val();
    	    timeE=$('#etaTiIn').val();
    	    if((a=timeS.match(/(\d{2}):(\d{2})/))&&(b=timeE.match(/(\d{2}):(\d{2})/))){
    	       if(parseInt(a[1])*60+parseInt(a[2])<=parseInt(b[1])*60+parseInt(b[2])){
    	           adjBar(this,$('#staTiIn').val(),$('#etaTiIn').val());   
                   $(this).dialog('close');    
    	       }else{
    	           alert('開始時間需小於結束時間');
    	       }   
    	    }else{
    	        alert('不符合格式');
    	    }
    	    }
    	
    },{
    	text:'cancel',
    	click:function(){$(this).dialog('close');}
    }],resizeable:false,width:300,height:310,modal:true});
    
    /**********************************************
     * function:
     *  set the function to all close or all open for the time
     *  range input
     * argument:
     * return:
     **********************************************/
    $('#allopenTRI').click(function(){
        getTIMEthenCLICK($('#staTiIn'),$('#etaTiIn'),1);    
    });
    $('#allcloseTRI').click(function(){
        getTIMEthenCLICK($('#staTiIn'),$('#etaTiIn'),0);
    });
    
	$("#TimeRan").dialog({
    title:'Time Range',autoOpen: false,
    resizeable:false,width:180,height:100, position:[100,100]});
    
    
    $("#passCHANGEdialog").dialog({
    title:'更改密碼',autoOpen: false,dialogClass:'vboxDialogContent',
    resizeable:false,width:320,height:250, position:[100,100],modal:true,open:function(){
    	$('#oldPASS').val('');
        $('#newPASS1').val('');
        $('#newPASS2').val('');	
    },
    buttons:[{text:"確認",click:function(){
        
        oldPASScheck=false;
        gg['pass']=$('#oldPASS').val();
        gg['id']="wandog";
        gg['fn']='loginPROCESS';
        jQuery.post('sqlite_test.php',gg,function(d){
            oldPASScheck=d.login;
            if(oldPASScheck==false){
                alert('舊密碼錯誤');
                return;
            }else{
                if($('#newPASS1').val()==''||$('#newPASS1').val()==''){
                    alert('新密碼不可空白');
                    return;
                }
                
                if($('#newPASS1').val()==$('#newPASS2').val()){
                    gg['pass']=$('#newPASS1').val();
                    // gg['id']="wandog";
                    gg['fn']='changePASS';
                    $.post('sqlite_test.php',gg,function(d){
                        alert('密碼變更成功');
                        return;
                    })
                }else{
                    alert('新密碼不一致 請重新輸入')
                }    
            }     
               
            },'json');
            
        
    }}]});
    
    $("#ChangeScript").dialog({
    title:'Script Select',autoOpen: false,dialogClass:'vboxDialogContent',
    resizeable:false,width:800,height:450,modal:true
    ,buttons:[{text:"select",click:function(){
        //below is used to change the script name display on webpage and relash the timebars corresponding to this latest script
        //if the order demand is not set, the change of script should be disabled!!!
        //below is the realization of description above
        // if(!optionSTATEtest($('#sel_1 :selected').text(),$('#sel_2 :selected').text(),$('#sel_3 :selected').text())){
            // return;    
        // }
        
        gg={};gg['fn']='ToCSed';gg['value']=$('#sel_script :selected').text();
        $.post('sqlite_test.php',gg,function(d){
            if(d.valueCheck==$('#sel_script :selected').text()){    
                $('#selected_script div').html($('#sel_script :selected').text());
                //alert($('#sel_1 :selected').text());
                getScriptBackO('direction','side','floor');
                // getScriptBackO($('#sel_1 :selected').text(),$('#sel_2 :selected').text(),$('#sel_3 :selected').text());   
            }else
                alert('script update error!!');
        },'json');
        
        $(this).dialog('close');
        
        }},
    {text:"cancel",click:function(){$(this).dialog('close');}}]
    });
    //////////////////////////////////////////////////////////////////////////////////
	//all these's argument of function including changeScript, changeM and etc was put in initializer.js
	var ScripTest=new ModScript('腳本選擇',"right top","center center",400,250);
	ScripTest.creBtn(true,changeScript,$('#script_button'),"after");
	
	var ModTest_1=new ModScript('自動模式',"left bottom","center center",50,1000);
	ModTest_1.creBtn(true,changeM,$('#mod_select'),"before");
	
	var timeType=new ModScript('平日',"left bottom","center center",50,1000);
	timeType.creBtn(true,changeT,$('#timeRange_select'),"before");
	
	//////////////////////////////////////////////////////////////////////////////////
	
	
	
	for(i=1;i<4;i++){
		$('#sel_'+i).css({'background-color':'#4C787E','width':200,'font-size':'25px','color':'#5CB3FF','border':'solid 3px #726E6D'}).corner('3px');	
	}
	// $('#reportG').css({'background-color':'#151B8D','width':180,
	// 'font-size':'25px','color':'#5CB3FF','border':'solid 3px #726E6D'}).
	// corner('3px');
	$('#reportG').html('產生腳本排序').css({'width':'150'}).
    corner('3px').hover(function(){
        $(this).addClass('btn1over');
    },function(){
        $(this).removeClass('btn1over');
    }).mousedown(function(){
        $(this).addClass('btnDOWN');
    }).mouseup(function(){$(this).removeClass('btnDOWN');});
    	
	//above: gen the button to gen bars as demand of order
	
	
	//this part is for to show the bars in the order of demand of user
    $('#reportG').click(function(){
    	if(!optionSTATEtest($('#sel_1 :selected').text(),$('#sel_2 :selected').text(),$('#sel_3 :selected').text())){
            return;    
        }
        getScriptBackO($('#sel_1 :selected').text(),$('#sel_2 :selected').text(),$('#sel_3 :selected').text());
   
})
	//add the dom of buttons of save as and save
	$('#saveas_button').html('另存新腳本').corner("3px").
        click(function(){
            $("#SaveAsDia").dialog('open');
        }).hover(function(){
            $(this).addClass('btn1over');
        },function(){
            $(this).removeClass('btn1over');
        }).mousedown(function(){
            $(this).addClass('btnDOWN');
        }).mouseup(function(){$(this).removeClass('btnDOWN');});
        
        //below is to register function on event of click
        $('#save_button').click(function(){
            // console.info('save action');
            var file=new saveAs();
            file.collectPs();   //collect the timebar's on and off time 
            file.transmmit();   //send the data collected back to sqlite    
        });
        
        //below is about the set of button "save"
        $('#save_button').html('儲存腳本').corner('3px').hover(function(){
            $(this).addClass('btn1over');
        },function(){
            $(this).removeClass('btn1over');
        }).mousedown(function(){
            $(this).addClass('btnDOWN');
        }).mouseup(function(){$(this).removeClass('btnDOWN');});
        
        //setup of btn logout
        $('#btnLOGOUT').click(function(){
            gg={};gg['fn']='logoutPROCESS';
            $.post('sqlite_test.php',gg,function(d){
                if(d==true){
                    window.location = "index.php"    
                }        
            },'json');
        });
        
        $('#batch_div').dialog({title:'batch setup',autoOpen: false,resizeable:false,width:600,height:350,modal:true});
        
        //set the function of button with value of "batch setup"
        $('input[type="button"][value="批次設定"]').click(function(){
            $('#batch_div').dialog('open');});
            
        $('#btnPASSchange').click(function(){
            $('#passCHANGEdialog').dialog('open');
        });
        
        
            
        //below is used to hide the option of "side" in the start of open of dialog of batch setup    
        $('#logic_side option:eq(0)').attr({'selected':'selected'});  
        $('#logic_side').hide(1);
        $('#DynaB').hide(1);
	
});
	
	
 
</script>
</head>
<body>
<center>
	
<h1 >HMI of LIS power control system</h1>

<table border="1" id="main_table" >
	<tr>
		<td id="timeRange_select" align="left">
		<td id="presentTime" class="tableB"></td>
		<td width="80">目前腳本:</td>    
		</td><td id="selected_script">
            <div></div>
        </td>
        <td id="mod_select" align="right"></td>
	</tr>
	<tr>
		<td colspan="4">
		<table border="1" id="test_2" >
			
		</table>	
		</td>
		<td align="left" valign="top">
		<table border="0" id="test_3" >
				<tr>
				<div>
				<select id="sel_1" >
				    <option value="default" selected="selected">第一順位</option>
					<option value="1">floor</option>
					<option value="2">direction</option>
					<option value="3">function</option>
					<option value="4">side</option>
				</select></br>
			
				<select id="sel_2">
					<option value="default">第二順位</option>
					
				</select></br>
			
				<select id="sel_3">
					<option value="default">第三順位</option>
					
				</select></br>
                <br><br>
                <div id="reportG" class="btn1"></div>    			
				<!-- <input type="button" value="generate" id="reportG"> --><br>
				
				</div>
				</tr>
				<tr>
				    <td><br><br><br><br></td>
				</tr>
				<tr>
				    <td id="script_button"></td>
				</tr>
				<tr>
                    <td ><div id="save_button" class="btn1"></div></td>
                </tr>
                <tr>
                    <td ><div id="saveas_button" class="btn1"></div></td>
                </tr>
                <tr>
                    <td><input type="button"  value="批次設定"></td>
				</tr>
				<tr>
                    <td><input type="button"  value="更改密碼" id="btnPASSchange"></td>
                </tr>
				<tr>
				    <td><input type="button" value="登出" id="btnLOGOUT"></td>
				</tr>
		</table>	
		</td>
	</tr>
</table>
<!--below table is for function buttons-->

<table border="1" id="button_table">
	<tr>
		<!-- <td id="selected_script">
		    <div></div>
		</td> -->
		
	</tr>
</table>

<div id="SaveAsDia"><!--it's javascript code is put in scriptIO.js-->    
	<form>
		<table >
			<tr>
				<td>
					<select id="fileOpt" size="5">
					<option value="default">default</option>
					<option value="default1">default1</option>
					</select>	
				</td>
				<td valign="bottom">
					<input type="button" value="save">
					<input type="button" value="delete">
					<input type="button" value="cancel" id="celSaveAs">
					
				</td>
			</tr>
			
			<tr>
				<b>script name:</b> <input type="text" id="filename" value="script name">
				
			</tr>
			<tr>
			    <br>
			    <b>time mode: <span id="time_mode_dialog"></span></b>
			</tr>
		</table>
	</form>
</div>

<div id="ChangeScript">
	<form>
		<table>
			<tr>
				<td>
					<img id="script_img" src="monkey_1.jpg" width="50">
				</td>
				<td>
				起始日期:<BR><input type="text" id="dateSTART" class="textINPUT"><BR>
                結束日期:<BR><input type="text" id="dateEND" class="textINPUT"><BR><br>    
				    
				可選擇的腳本<br>
				<select size="4" id="sel_script">
  				<!--add existing script here-->
				</select>
				</td>
				
				<td>
					<input type="button" value="設定寒假腳本" id="setBTNwin"><br>
					<span id="scriWIN">aa</span><BR>
					<input type="button" value="設定暑假腳本" id="setBTNsum"><br>
					<span id="scriSUM">bb</span><BR>
					<input type="button" value="設定學期中腳本" id="setBTNlear"><br>
					<span id="scriLEARN">cc</span><BR>
						
						
				</td>
				<td>
				    <input type="button" value="設定特殊日期與腳本" id="setTIMEspeci"><br>
					特殊日期(腳本===時間範圍)<br>
                    <select size="4" id="sel_speci">
                    <!--add existing script here-->
                    </select><br>
                    <input type="button" id="delSPECIdate" value="刪除"><br>  
					
				</td><td>	
					<input type="button" value="設定寒假日期" id="setTIMEwin"><br>
					<span id="dateWIN">aa</span><BR>
					<input type="button" value="設定暑假日期" id="setTIMEsum"><br>
					<span id="dateSUM">aa</span><BR>
					
				</td>
				<!-- <td>
					
						
				</td> -->
			</tr>
		</table>
	</form>
</div>
<script type="text/javascript">
	
	
	$(document).ready(function(){
// 		insert special date for schedule
		$('#setTIMEspeci').click(function(){
			if((!testSCRIPTsel())||(!abINPUTdetect())){
				return;
			}
			
			var sel=$('#sel_script :selected').text();
			var dS=$('#dateSTART').val();
			var dE=$('#dateEND').val();
			// var script=$('#sel_script :selected').text();
			
			gg={};gg['fn']='setSPECI';gg['ope']='insert';
			gg['dateS']=dS;gg['dateE']=dE;
			gg['script']=sel;
			// gg['PERIODname']='wintervacation';
			
			$.post("sqlite_test.php",gg,function(d){
				if(d=='got it'){
					reflashSCHEDULE();
				}else{
					if(d=='duplicate'){
						alert('有時間重疊!');
						reflashSCHEDULE();
					}
				}
			},'json');
		});
		
		
// 		del the special date from schedule
		$('#delSPECIdate').click(function(){
			
			
			
			var sel=$('#sel_speci :selected').text();
			// console.info()
			var DATA=sel.split(/===/);
			var script=DATA[0];
			var dateA=DATA[1].split(/~/);
			// console.info(dateA[0]);
			// console.info(dateA[1]);
			gg={};gg['fn']='setSPECI';gg['ope']='del';
			gg['script']=script;
			gg['dateS']=dateA[0];gg['dateE']=dateA[1];
			$.post('sqlite_test.php',gg,function(d){
				if(d=='got it'){
					reflashSCHEDULE();	
				}
			},'json');
		})
		
//		insert the script for wintervacation
		$('#setBTNwin').click(function(){
			if(!testSCRIPTsel()){
				return;
			}
			// if($('#sel_script :selected').text()==""){
				// alert('你沒有選擇腳本');
				// return;
			// }
			gg={};gg['fn']='setALLKINDscript';
			gg['script']=$('#sel_script :selected').text();
			gg['PERIODname']='wintervacation';
			$.post("sqlite_test.php",gg,function(d){
				if(d=='got it'){
					$('#scriWIN').html($('#sel_script :selected').text());
					alert('更新成功');
				}
			},'json');
		});
//		insert the script for summervacation
		$('#setBTNsum').click(function(){
			if(!testSCRIPTsel()){
				return;
			}
			// if($('#sel_script :selected').text()==""){
				// alert('你沒有選擇腳本');
				// return;
			// }
			gg={};gg['fn']='setALLKINDscript';
			gg['script']=$('#sel_script :selected').text();
			gg['PERIODname']='summervacation';
			$.post("sqlite_test.php",gg,function(d){
				if(d=='got it'){
					$('#scriSUM').html($('#sel_script :selected').text());
					alert('更新成功');
				}
			},'json');
		});
//		insert the script for normal time
		$('#setBTNlear').click(function(){
			if(!testSCRIPTsel()){
				return;
			}
			// if($('#sel_script :selected').text()==""){
				// alert('你沒有選擇腳本');
				// return;
			// }
			var gg={};
			gg['fn']='setALLKINDscript';
			gg['script']=$('#sel_script :selected').text();
			gg['PERIODname']='inLEARNING';
			$.post("sqlite_test.php",gg,function(d){
				if(d=='got it'){
					$('#scriLEARN').html($('#sel_script :selected').text());
					alert('更新成功');
				}
			},'json');
		});
		
		
		
		var a=['setTIMEwin','setTIMEsum'];
		// var b=['wintervacation','summervacation'];
		for(var i in a){
			// console.info('#'+a[i]);
			$('#'+a[i]).click(function(){
				if(!abINPUTdetect()){
					return;
				}
				
				var gg={};
				gg['fn']='setALLKINDdate';
				// gg['script']=$('#sel_script :selected').text();
				if($(this).attr('id')=='setTIMEwin'){
					gg['PERIODname']='wintervacation';	
				}else{
					gg['PERIODname']='summervacation';
				}
				gg['dateS']=$('#dateSTART').val();
				gg['dateE']=$('#dateEND').val();
				$.post("sqlite_test.php",gg,function(d){
					if(d=='got it'){
						reflashSCHEDULE();
						alert('更新成功');		
					}
				},'json');
			});
			
		}
		
		
		$('#ChangeScript').dialog({open:function(){
			reflashSCHEDULE();		
		}});
		
		

		setTimeout(function(){
		$('td[name=timeONrange]').trigger('showON');
		
		},1500);
		
		
	});
//test if the date range and format correct!	
	var abINPUTdetect=function(){
		var dateCORRECT1=$('#dateSTART').val().match(/\d{4}-\d{2}-\d{2}/);
		var dateCORRECT2=$('#dateEND').val().match(/\d{4}-\d{2}-\d{2}/);
		// console.info(dateCORRECT1);
		// console.info(dateCORRECT2);
		//check the format of date
		if(!dateCORRECT1 || !dateCORRECT2){
			alert('日期格式不正確');
			return false;
		}
		//check the range of date
		// console.info($('#dateSTART').val().split(/-/));
		var splitDATE1=$('#dateSTART').val().split(/-/);
		var splitDATE2=$('#dateEND').val().split(/-/);
		var date1=new Date(splitDATE1[0],splitDATE1[1],splitDATE1[2]);
		var date2=new Date(splitDATE2[0],splitDATE2[1],splitDATE2[2]);
		if(date2<date1){
			alert('結束日期小於開始日期');
			return false;
		}
		
		return true;	
	};
	
// 	test if the script is selected!
	var testSCRIPTsel=function(){
		if($('#sel_script :selected').text()==""){
			alert('沒有選擇腳本');
			return false;
		}
		return true;
	};
	var reflashSCHEDULE=function(){
		gg={};gg['fn']='getALLKINDscript';
			$.post('sqlite_test.php',gg,function(d){
				$('#scriWIN').empty().html(d.win);
				$('#dateWIN').empty().html(d.winS+"~"+d.winE);	
					
				$('#scriSUM').empty().html(d.sum);
				$('#dateSUM').empty().html(d.sumS+"~"+d.sumE);
				$('#scriLEARN').empty().html(d.learn);
				
				for(var i in d.specified){
					// console.info(d.specified[i].script);
				var option=$('<option>').html();
				}
					
			},'json');
			
		gg={};gg['fn']='setSPECI';
				gg['ope']='readback';
				$.post('sqlite_test.php',gg,function(d){
					$('#sel_speci').empty();
					if(d){
						$.each(d,function(key,value){
							// console.info(key);
							// console.info(value);
							var opt=$('<option>').text(value.script+"==="+value.dateRANGE);
							$('#sel_speci').append(opt);		
						});	
					}
					
				},'json');
	};
	
</script>
<div id="TimeRan">
	<form>
		<table>
			<tr>
				<td id='plaShowTime'></td>
			</tr>
		</table>
	</form>
</div>

<div id="TimeInput">
	<form>
		<table>
		    <tr>
		        <input type="button" value="整天開啟" align="right" id="allopenTRI">
                <input type="button" value="整天關閉" align="right" id="allcloseTRI"><br><br>    
		    </tr>
			<tr>
				<td>
					Start:	
				</td>
				<td>
					<input type="text" value="enter start time" id="staTiIn">
				</td>
				
			</tr>
			<tr>
				<td>
					End:	
				</td>
				<td> 
					<input type="text" value="enter end time" id="etaTiIn">
				</td>
			</tr>
		</table>
	</form>
</div>
<div id="passCHANGEdialog">
    <form>
        <table>
            <tr>
            <span>舊密碼<input type="password" id="oldPASS"></span>    
            </tr>
            <tr>
            <span>新密碼<input type="password" id="newPASS1"></span>    
            </tr>
            <tr>
            <span>再確認<input type="password" id="newPASS2"></span>    
            </tr>
            <tr>
                
            </tr>
        </table>
    </form>
</div>

<div id="batch_div">
    <form>
        <table border="1">

        <tr>
            <td>
            <SPAN><B>FLOOR</B><BR>
            <select id="logic_floor" multiple size="8">
                
                <option value="3">3rd floor</option>
                <option value="4">4th floor</option>
                <option value="5">5th floor</option>
                <option value="6">6th floor</option>
                <option value="7">7th floor</option>
                <option value="8">8th floor</option>
                <option value="9">9th floor</option>
                <option value="10">10th floor</option>
            </select>
            </SPAN>
            </td>
            <td><SPAN>
            <B>DIRECTION</B><BR>
            <select id="logic_direction" multiple size="2">
                
                <option value="east">east</option>
                <option value="west">west</option>
            </select><BR>
            
            <B>FUNCTION</B><BR>
            <select id="logic_function" multiple size="2">
                
                <option value="air">air</option>
                <option value="light" id="sel_trigger">light</option>
            </select><BR>
            <B id="DynaB">SIDE</B><BR>
            <select id="logic_side" multiple size="2">
                
                <option value="side">side</option>
                <option value="kernel">kernel</option>
            </select></SPAN>
            </td>
            <td>
                <div id="div_showTable">
                    
                    <ul id="showTableBat">
                        
                    </ul>   
                
                </div>
                <div>
                <input type="button" value="整天開啟" align="right" id="allopenTBB">
                <input type="button" value="整天關閉" align="right" id="allcloseTBB"><br><br>
                <span>start time:</span><input type="text" id="batch_s"><br>
                <span>end   time:</span><input type="text" id="batch_e"><br><br>
                <input type="button" value="set" align="right" id="changeTBB">        
                
                </div>
            </td>
        </tr>
        
        
        </table>
        

        <input type="button" value="mass_select">
    </form>
</div>

</center>

</body>
<script type="text/javascript">
$('#logic_function').change(function(event){
    //console.info($(this).val());
    
    var flagSelLight;
    for(var i in $(this).val()){
        //0->air;1->light
        //console.info(i);
        if($(this).val()[i]=='light'){
            flagSelLight=true;
        }else{
            flagSelLight=false;
        }
    }
    //console.info(flagSelLight);
    if(flagSelLight!=true){
        $('#DynaB').hide(10);
        $('#logic_side').hide(10);
        $('#logic_side option:eq(0)').attr({'selected':'selected'});        
    }else{
        $('#DynaB').show(10);
        $('#logic_side').show(10);
    }
});


/**************************************************************
 * function:
 *  set time range as all open or all close and then click the setup button
 * arugemnt:
 *  timeS:
 *      the time to start
 *  timeE:
 *      the time to close
 *  CLICK:
 *      the element to trigger click action
 *  OorC:
 *      want to all open or all close in a day;1 all open/0 all close 
 * return:
 *  none
 **************************************************************/
var getTIMEthenCLICK=function(timeS,timeE,OorC){
    if(OorC==1){//ALL OPEN
        timeS.val('00:01');
        timeE.val('24:00');    
    }else{      //ALL CLOSE
        timeS.val('00:01');
        timeE.val('00:01');
    }
    // eleCLICK.click();
}

/******************************************************************
 * function: adjust the position of input bar of batch setup dialog
 * 
 ******************************************************************/

$('#batch_e').position({my:"left center",at:"left bottom", of:$('#batch_s') ,
offset:"0 +18",collision:"none"});
/*****************************************************************
 * function:
 *  set time as all open or all close for batch setup dialog
 *****************************************************************/
$('#allopenTBB').click(function(){
    getTIMEthenCLICK($('#batch_s'),$('#batch_e'),1);
});
$('#allcloseTBB').click(function(){
    getTIMEthenCLICK($('#batch_s'),$('#batch_e'),0);
});

$('#changeTBB').click
/*******************************************************************
 * function: 
 *      register the funtion of chhange the timebat in batch mode at the
 *      button of "set" of dialog of batch setup
 * arguments:
 * return:
 *******************************************************************/
$('#changeTBB').click(function(){
    if($('#showTableBat').html()==null){
        alert('沒有選擇任何控制點!');
        return;        
    }
    //if the response is null, then return
    
    //handle the attach of this point filtered out
    timeS=$('#batch_s').val();
    timeE=$('#batch_e').val();
//      below is the check for format of time   
    if((a=timeS.match(/(\d{2}):(\d{2})/))&&(b=timeE.match(/(\d{2}):(\d{2})/))){
       if(parseInt(a[1])*60+parseInt(a[2])<=parseInt(b[1])*60+parseInt(b[2])){
           
            i=0;
            var ele={};
            while($('#div_showTable').data('node')[i]){     
                var id_node=$('#div_showTable').data('node')[i];
                ele[i]=$("[id*=div_off]").filter(function(index){
                    return ($(this).data('node')==id_node);
                });
                i++;
            }
            i=0;
            while(ele[i]){
                //console.info(ele[i]);
                var id=ele[i].children().attr('id');
                adjBar(null,$('#batch_s').val(),$('#batch_e').val(),$('#'+id));
                i++;
            }   
               
       }else{
           alert('開始時間需小於結束時間');
       }   
    }else{
        alert('不符合格式');
    }
                
});
    
    
/*
*function:
*register the click function on "batch setup" button  
*the function of this button is to collect the node corresponding to filter of options
*  
*/      
    $('input[type="button"][value="mass_select"]').click(function(){
        var jsonItem={};
        //should add a mechanism to avoid specified select is not selected
        $('select[id*=logic]').each(function(index){
            jsonItem[index]=$(this).val();});
        //post data to getback the list of point on demand
        gg={};
        gg['fn']='batch_select';
        gg['item']=jsonItem;
        $.post('sqlite_test.php',gg,function(d){
            //if there have been items, then clear them
            if($('#showTableBat').html()){
                $('#showTableBat').empty();    
            }else{
			}
            //if the response is null, then return
            if(d==null){
                alert('no point is selected!');
                return;
            }else{
                $('#div_showTable').data('node',d.point).data('fullname',d.ss);//.dialog('open');    
               
                for(var i in $('#div_showTable').data('fullname')){
                    var stringName=$('#div_showTable').data('fullname')[i].replace(/side_kernel/gi,'');
                    //var test=stringName.;
                    $('#showTableBat').append($('<li>').html(stringName));
                }
            }
        },'json');       
    })
	
	
	
		
	
</script>



</html>