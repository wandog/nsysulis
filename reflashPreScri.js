/**
 * @author jhma
 */
/*
 * function: polling all the state it is from sqlite and 
 *           ask the php file to set the 'shouldbe' according to the
 *           present time and the selected script
 */
var reflashPreScri=function(){
    gg={};gg['fn']='POLLnUP';
    $.post('sqlite_test.php',gg,function(d){
        console.info(d);
    },'json');    
        
}
