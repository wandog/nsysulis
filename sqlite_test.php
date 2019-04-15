<?php
require_once(dirname(__FILE__).'/sql_header.php');
session_start();

/**
 * Simple example of extending the SQLite3 class and changing the __construct
 * parameters, then using the open method to initialize the DB.
 */

try{
	$db = new PDO('sqlite:power_control.db');	
}
catch( PDOException $e ){
	die( $e->getMessage() );
}


function N2alphabet($num){
	if($num>0 && $num<=26) {
 		return chr(96+$num);
	}else{
		return 'error';
	}
}

function delScript($db,$sn,$tt){
    // mysql_query("delete from scripts where script='".$sn."' and time_type='".$tt."';");
    $query="delete from scripts where script='".$sn."' and time_type='".$tt."';";
    mysql_query($query);
}

function fbACorder($O1,$O2,$O3,$S,$T,$db){
    $switch='on';
    // $sql="select controlnode.controlpoint, time, floor, direction, function from controlnode left join scripts on
      // scripts.script='".$S."' and scripts.time_type='".$T."' and scripts.switch='".$switch."' and
       // controlnode.controlpoint=scripts.controlpoint order by ".$O1.",".$O2.",".$O3.";";
    $sql="select * from (select controlnode.controlpoint as node, time as time_1, floor, direction, function from controlnode left join 
    scripts on scripts.script='".$S."' and scripts.time_type='".$T."' and scripts.switch='".$switch."' and
    controlnode.controlpoint=scripts.controlpoint ) as t left join nodeside on node=nodeside.controlpoint order by ".$O1.",".$O2.",".$O3.";";   
    //echo $sql;
    //echo $sql.'<br>';
    $result=mysql_query($sql);
    $i=0;
    while($row = mysql_fetch_array($result)){
        if($row['side']){
            $d[$i]['bar_name']=$row['floor']."_".$row['direction']."_".$row['function']."_".$row['side'];    
        }else{
            $d[$i]['bar_name']=$row['floor']."_".$row['direction']."_".$row['function'];
        }
        
        $d[$i]['on_time']=$row['time_1'];
        $d[$i]['controlpoint']=$row['node'];//$row['controlpoint'];
        $i=$i+1;
    }
    //for get time of switch off
    $switch='off';
    // $sql="select controlnode.controlpoint as node, time, floor, direction, function from controlnode left join scripts on scripts.script='".$S."' and scripts.time_type='".$T."' and 
    // scripts.switch='".$switch."' and controlnode.controlpoint=scripts.controlpoint order by ".$O1.",".$O2.",".$O3.";";
    $sql="select * from (select controlnode.controlpoint as node, time as time_1, floor, direction, function from controlnode 
    left join scripts on scripts.script='".$S."' and scripts.time_type='".$T."' and 
    scripts.switch='".$switch."' and controlnode.controlpoint=scripts.controlpoint) as t left join nodeside on node=nodeside.controlpoint order by ".$O1.",".$O2.",".$O3.";";
    //echo $sql.'<br>';
    
    $result=mysql_query($sql);
  
    $i=0;
    while($row = mysql_fetch_array($result)){
        //$data[$i]['bar_name']=$row['floor']."_".$row['direction']."_".$row['function'];
        $d[$i]['off_time']=$row['time_1'];
        
        $i=$i+1;
    }
    //echo $data;
    return $d;   
}




switch($_POST['fn']){
	case "getSELECTEDscript":
		$sql="select script from scheduleSCRIPT union select script from scheduleSPECI;";
		$result=mysql_query($sql);
		$i=0;
		while($row=mysql_fetch_array($result)){
			$data[$i]=$row['script'];	
			$i++;
		}
		break;	
	case "setSPECI":
		if($_POST['ope']=='insert'){
			$sql="select * from scheduleSPECI where (dateS<='".$_POST['dateS']."' and dateE>='".$_POST['dateS']."' ) or (dateS<='".$_POST['dateE']."' and dateE>='".$_POST['dateE']."');";
			// echo $sql;
			$result=mysql_query($sql);
			if($row=mysql_fetch_array($result)){
				$data='duplicate';
				break;
			}
			
			$sql="insert into scheduleSPECI(dateS,dateE,script) values('".$_POST['dateS']."','".$_POST['dateE']."','".$_POST['script']."');";	
			mysql_query($sql);
			$data="got it";
		}else{
			
			if($_POST['ope']=='readback'){
				// echo "hi";
				$sql="select * from scheduleSPECI;";
				$result=mysql_query($sql);
				$i=0;
				while($row=mysql_fetch_array($result)){
					$data[$i]['dateRANGE']=$row['dateS']."~".$row['dateE'];
					$data[$i]['script']=$row['script'];
					$i++;
				}	
			}else{
				// echo 'hi';
				if($_POST['ope']=='del'){
					$sql="delete from scheduleSPECI where dateS='".$_POST['dateS']."' and dateE='".$_POST['dateE']."' and script='".$_POST['script']."';";
					// echo $sql;
					mysql_query($sql);
					$data='got it';
				}
				
			}
				
			
		}
		
		break;
	case "setALLKINDdate":
		$sql="update scheduleSCRIPT set dateS='".$_POST['dateS']."', dateE='".$_POST['dateE']."' where PERIODname='".$_POST['PERIODname']."';";
		mysql_query($sql);
		$data='got it';
		break;
	case "setALLKINDscript":
		$sql="update scheduleSCRIPT set script='".$_POST['script']."' where PERIODname='".$_POST['PERIODname']."';";
		mysql_query($sql);
		$data='got it';
		break;
	case "getALLKINDscript":
// 		code below is ugly and should be modified later
		$sql="select script,dateS,dateE from scheduleSCRIPT where PERIODname='wintervacation'";
		$result=mysql_query($sql);
		$row=mysql_fetch_array($result);
		$data['win']=$row['script'];
		$data['winS']=$row['dateS'];
		$data['winE']=$row['dateE'];
		
		$sql="select script,dateS,dateE from scheduleSCRIPT where PERIODname='summervacation'";
		$result=mysql_query($sql);
		$row=mysql_fetch_array($result);
		$data['sum']=$row['script'];
		$data['sumS']=$row['dateS'];
		$data['sumE']=$row['dateE'];
		
		
		$sql="select script,dateS,dateE from scheduleSCRIPT where PERIODname='inLEARNING'";
		$result=mysql_query($sql);
		$row=mysql_fetch_array($result);
		$data['learn']=$row['script'];
		
		$sql="select script,dateS,dateE from scheduleSCRIPT where PERIODname<>'inLEARNING' and PERIODname<>'summervacation' and PERIODname<>'wintervacation'";
		$result=mysql_query($sql);
		
		$i=0;
		while($row=mysql_fetch_array($result)){
			$data['specified'][$i]['script']=$row['script'];
			$data['specified'][$i]['dateS']=$row['dateS'];
			$data['specified'][$i]['dateE']=$row['dateE'];
			$i++;
		}
		
		
		break;
    case "changePASS":
        $sql="update users set pass='".md5($_POST['pass'],false)."';";
        $result=mysql_query($sql);
        
        break;
    case "logoutPROCESS":
        unset($_SESSION);
        $data=true;
        break;
    case "loginPROCESS":
        $sql="select * from users where id='".$_POST['id']."' and pass='".md5($_POST['pass'],false)."';";
        //echo md5($_POST['pass'],false);
        // $result=mysql_query($sql);
        $result=mysql_query($sql);
        // $row=mysql_fetch_array($result);
        $row=mysql_fetch_array($result);
        // echo $row['id'];
        if($row){
            $_SESSION['login']=true;
            $data['login']=true;
            //cho $_SESSION['login'];
        }else{
            $_SESSION['login']=false;
            $data['login']=false;
            //echo $_SESSION['login'];
        }
        break;
    case "operationMANUAL":
        $mode=getSELECTEDmode($db);
        if ($mode=="auto"){
            $data['mode']='auto';    
        }else{
            $data['mode']='manual';
            $result=mysql_query("select shouldbe from presentscript where controlpoint='".$_POST['node']."';");
            
            $row=mysql_fetch_array($result);
            //echo $row['shouldbe'];
            if($row['shouldbe']==1){//toggle "shouldbe"
                  //from true to false  
                //echo "update presentscript set shouldbe=0 where controlpoint='".$_POST['node']."';";
                mysql_query("update presentscript set shouldbe=0 where controlpoint='".$_POST['node']."';");    
            }else{//from false to true
                mysql_query("update presentscript set shouldbe=1 where controlpoint='".$_POST['node']."';");
            }
            
        }
        
        
                
    break;
    case 'Pooling': //to get real relay state back
            
            //polling the state of 'itis'
            $result=mysql_query("select itis from presentscript where controlpoint='".$_POST['node']."';");
            //$i=0;
            while($row = mysql_fetch_array($result)){
                //$data[$i]['point']=$row['controlpoint'];
                $data['itis']=$row['itis'];
                //$data[$i]['shouldbe']=$row['shouldbe'];
                //$i++;
            }
        break;
    case 'DelScript':
        $sn=$_POST['DelTar'];
        delScript($db,$sn,"normal");//del normal time
        delScript($db,$sn,"weekend");//del weekend time
    break;    
    case 'ToCSed'://to change the script in selected
        $result=mysql_query("update selected set script='".$_POST['value']."';");
        
        $result=mysql_query("select script from selected;");
        
        if($row=mysql_fetch_array($result))
            $data['valueCheck']=$row['script'];
    break;
    
    case 'BtnHandle':
        switch($_POST['button']){
            case 'timeRange':
                // echo "update selected set time_type='".$_POST['state']."';";
                mysql_query("update selected set time_type='".$_POST['state']."';");
                $result=mysql_query("select time_type from selected;");
                if($row=mysql_fetch_array($result))
                   $data['valueCheck']=$row['time_type'];
            break;
                
            case 'mode':
                mysql_query("update selected set mode='".$_POST['state']."';");
                $result=mysql_query("select mode from selected;");
                if($row=mysql_fetch_array($result))
                    $data['valueCheck']=$row['mode'];
            break;
        }
    break;
    
    case 'GetSSData':   //get the state of table of selected~
        $sql="select script,time_type from selected;";
        $result=mysql_query($sql);
        $row=mysql_fetch_array($result);
        $data['TTinital']=$row['time_type'];    
        $data['scriptInitial']=$row['script'];
        $data['modeInitial']=getSELECTEDmode($db);
        //$a=getSELECTEDmode($db);
        //echo $a;
    break;    
    case 'FBbyOrder':   //get the controlnode's data back according the demand of order
        
        
        $data=fbACorder($_POST['order'][1],$_POST['order'][2],$_POST['order'][3],$_POST['script'],$_POST['time_type'],$db);
        
        
    break;
	case 'getTType':
		$result=mysql_query('select script,time_type from selected;');
		while($row = mysql_fetch_array($result))		
		{
			$data['script']=$row['script'];
			$data['time_type']=$row['time_type'];
			//$i=$i+1;
		}
	break;
	case 'c2a':
		$data=N2alphabet(1);
	break;
	
	case 'getScript':
		$result = mysql_query('SELECT distinct script as name FROM scripts');//if the query has error, the result returned will not be a object
	
	
		$i=0;
	
	//$result=mysql_query($sql);
			
		while($row = mysql_fetch_array($result))		
		{
			$data[$i]=$row['name'];
			
			$i=$i+1;
		}		
	break;
    case 'updateNodeT':
        //$data=$_POST['data'][1];
        //$result=mysql_query("update scripts set script='".$_POST['value']."';");
	   $Ast=getSelected($db);
	   
       $tt=$Ast[0];$sn=$Ast[1];
       delScript($db,$sn,$tt);//delete the old script for specifed tt and sn
	   
	   
	   $i=0;
	   while($_POST['data'][$i]){
	       $nodename=$_POST['data'][$i]['node'];
           $onTime=$_POST['data'][$i]['on'];
           $offTime=$_POST['data'][$i]['off'];
           
           
	           
	       mysql_query("insert into scripts (controlpoint,script,time_type,switch,time) values('".$nodename."','".$sn."','".$tt."','on','".$onTime."');");
	       mysql_query("insert into scripts (controlpoint,script,time_type,switch,time) values('".$nodename."','".$sn."','".$tt."','off','".$offTime."');");
	       $i++;
	   }
       $data="got it";
       //mysql_query
       
	break;
    
    
    
    case 'batch_select':
        
        // mysql_query("create table test_1 as select controlnode.controlpoint as point, floor, direction,function,side from controlnode left join nodeside on controlnode.controlpoint=nodeside.controlpoint;");         
//         
        // mysql_query("update test_1 set side='side_kernel' where side is Null;");//<---pay attention: this could be also used in node's full name display   
//         
        // mysql_query("create view temp_1 as select point, (\"floor\"||'_'||\"direction\"||'_'||\"function\"||'_'||\"side\") as ss from test_1;");
        getFull($db,true);
        //mysql_query("drop table test_1;");
        
        //mysql_query();
        $logicString="select * from temp_1 where ";
        
        $i=0;$j=0;
        $size_1=sizeof($_POST['item']);
        for($j=0;$j<4;$j++){
            $size_2=sizeof($_POST['item'][$j]);
            
            if($_POST['item'][$j][$i]){
                $logicStrOr="";
            }
            else{
                $logicStrOr="1=0";    
            }
            while($_POST['item'][$j][$i]){
                 
                    if($i==0){
                        $logicStrOr="ss like '%".$_POST['item'][$j][$i]."%'";            
                    }else{
                        $logicStrOr=$logicStrOr."or ss like '%".$_POST['item'][$j][$i]."%'";
                    }
                $i++;
            }
            $i=0;
            if($j==$size_1-1){//has reach the last logic item for and operation
                $logicString=$logicString."(".$logicStrOr.");";
            }else{  
                $logicString=$logicString."(".$logicStrOr.") and ";    
            }
                    
        }
        //echo $logicString;
        $result=mysql_query($logicString);
        // echo $logicString;
        // mysql_query("drop view if exists temp_1;");
        
        
        // $i=0;
//  
        while($row = mysql_fetch_array($result)){
            $data['point'][$i]=$row['point'];
            $data['ss'][$i]=$row['ss'];
            // echo $row['ss'];
            $i=$i+1;
        }
        
        // mysql_query("drop table if exists test_1;");
               
        // $result=mysql_query("select ss from temp_1 where point='g';");
        // $row = mysql_fetch_array($result);
        // echo $row['ss'];       
          
    break;
	default:
		
}

echo json_encode($data);


function getFull($db,$bool){
    mysql_query("drop view if exists temp_1;");
    mysql_query("drop table if exists test_1;");
    mysql_query("create table test_1 as select controlnode.controlpoint as point, 
    floor, direction,function,side from controlnode left join nodeside on controlnode.controlpoint=nodeside.controlpoint;");         
            
    if($bool==true){
        $side="side_kernel";       
    }else{
        $side="";    
    }
    mysql_query("update test_1 set side='".$side."' where side is Null;");//<---pay attention: this could be also used in node's full name display
    // mysql_query("create view temp_1 as select point, (\"floor\"||'_'||\"direction\"||'_'||\"side\"||'_'||\"function\") as ss from test_1;");
    mysql_query("create view temp_1 as select point, concat_ws('_',floor,direction,side,function) as ss from test_1;");
}

//add parenthesis according the num rested
function AddParen($num){
    switch($num){
        case 2:
            return ")))";
            break;
        case 3:
            return "))";
            break;
        case 4:
            return ")";
            break;
    }
}
/*
for the mapping of number to logic
ex. 1 to and; 2 to or
 */
function Mapping($p1){
    if($p1==1){
        return "and";
    }else{
        return "or";
    }
}

function getSelected($db){
    $sql="select script,time_type from selected;";
    $result=mysql_query($sql);
    $row=mysql_fetch_array($result);
    $tt=$row['time_type'];    
    $sn=$row['script'];
    
    return array($tt,$sn);
}


function getSELECTEDmode($db){
    $sql="select mode from selected;";
    $result=mysql_query($sql);
    $row=mysql_fetch_array($result);
    $mode=$row['mode'];    
    // $sn=$row['script'];
    return $mode;
    // return array($tt,$sn);
}


//mysql_fetch_array($result);
//echo $data;

?>