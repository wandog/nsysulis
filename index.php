<?php
session_start();
if(!empty($_SESSION['login'])){
    session_unset();    
}

$_SESSION['login']=false;

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
    
    <LINK REL="SHORTCUT ICON" HREF="ICO.ico">
    <title>nsysu lis power control</title>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
    <link type="text/css" href="css/sunny/jquery-ui-1.8.24.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
    <script type="text/javascript" src="js/jquery-ui-1.8.24.custom.min.js"></script>


</head>

<body>


<script type="text/javascript">
    
    //error times count
    var failcount=0;
    var ShowLofail=function(d){
        if(d['login']==false){
            //$("showinfo").empty().html("");
            failcount=failcount+1;
            var failstring='fail:'+failcount;
            $("#showinfo").empty().html(failstring);
        }else{   
            if(d['login']==true){   
                window.location.href='control_panel.php';       
            }
        }
    };
    
    
    $(document).ready(function(){
    // setTimeout(function(){
        // alert('hi');
        $('#dialog_1').dialog({'buttons':[
        {text:'Log in',click:function(){
            var gg={};
            gg['pass']=$('#p_pass').val();
            // document.cookie="pass="+$('#p_pass').val();
            // console.info(document.cookie.split(/;/));
            gg['id']=$('#p_id').val();
            gg['fn']='loginPROCESS';
            jQuery.post('sqlite_test.php',gg,function(d){
                ShowLofail(d);
                },'json');
        }}]});
    // },500);
          
    }
    );
    
</script>


 
<div id="dialog_1" title="中山大學圖書館電力系統">
    <form>
        <table id="entrance_power">        
            <tr><td>帳號:</td><td><input id="p_id" type="text"></td></tr>
            <tr><td>密碼:</td><td><input id="p_pass" type="password"></td></tr>
            <tr><td id="showinfo" cols="2"></td></tr>    
            
            <tr>
            <th ></th>
            </tr>
        </table>
    </form>
</div>





</body>
</html>