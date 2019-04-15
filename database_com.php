
<?php
require_once(dirname(__FILE__).'/sql_header.php');
//require_once(dirname(__FILE__).'/utils.php');
//$data=$_POST;


switch($_POST['fn']){
		
	case 'regi':
		$sql="insert into `id` (name,password) values ('".$_POST['id_apply']."','".$_POST['password_apply']."')";
		try{
			mysql_query($sql);
		}
		catch(Exception $e){
			$data["insert_status"]="false";
		}
		
	break;	

	case 'id_test':
	
	
	//$sql = "select * from `id` which name='".$id_check."';";
	$sql="SELECT * FROM `id` WHERE name='".$_POST['id_apply']."';";
	
	$result=mysql_query($sql);
	
	
	
	if(!$result) 
		{
		
		$data["check_status"]=$result;
		$data["sql_command"]=$sql;
		
		}
	else
	
		{
		if(@mysql_result($result,0)==null)
			$data["check_status"] ="no repeat";	
		else 
			$data["check_status"] ="repeat";
		
		}
	break;

	
	
	default:
		
	break;
	
}
	
echo json_encode($data);


?>