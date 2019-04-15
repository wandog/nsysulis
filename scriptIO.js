/**
 * @author jhma
 */
//arguments:
//  dsName:desired script name
function saveAs(dsName){    //
        var self=this;
        this.filename=dsName||'none';
        this.timebarData={};
        this.checkFlag=true;
        
        
        self.collectPs=function(){
           //collect all div of off bar
           var collect=$('[id*=div_off]');//<---this concept should also be used in batch setup
           //console.info(collect);
           var tbd=this.timebarData;//this is an action of reference assign
           
           collect.each(function(index){
               tbd[index]={'node':$(this).data('node'),'on':$(this).data('on_time_S').on, 'off':$(this).data('on_time_S').off};})
           return true;   
        };
        
        self.transmmit=function(){
            console.info(this.timebarData);
            gg={};gg['fn']='updateNodeT';gg['data']=this.timebarData;
            $.post('sqlite_test.php',gg,function(d){
            //console.info(d);
            if(d=='got it'){
            	alert('儲存成功');
            	reFlashOpe($('#fileOpt'));	
            }else{
            	alert('儲存失敗');
            }
                
           },'json');  
        };
        
        self.deleteS=function(ScriptName,SelDom){
            gg={};gg['fn']='GetSSData';
                    
            $.post('sqlite_test.php',gg,function(d){
                if(ScriptName==d['scriptInitial']){//the script want to del is the script selected
                    //pay attention: the script name in main screen should also be reflash after del
                    console.info('same with selected');
                    
                    ChangeToDefault();  //change the one showing to script named "default" before delete
                                        //present script
                    gg={};gg['fn']='DelScript';gg['DelTar']=ScriptName;
                    //console.info(DelScName);
                    $.post('sqlite_test.php',gg,function(d){
                        //reflash the display in optinos of dialog of "save as"
                        reFlashOpe(SelDom);//reflash the opts after del                               
                    },'json');
                                
                }else{  //the script want to del is not under display or selected. as a result, 
                        //it could be delete easily
                    gg={};gg['fn']='DelScript';gg['DelTar']=ScriptName;
                    //console.info(DelScName);
                    $.post('sqlite_test.php',gg,function(d){
                        //reflash the display in optinos of dialog of "save as"
                        reFlashOpe(SelDom);//reflash the opts after del                               
                    },'json');        
                }
                
            },'json');
        };
    }
/***********************************************************
 * function:
 *      change the script to the one named "default" when the script 
 *      going to be deleted is the one showing
 * arguments:
 *      none
 * return:
 *      none
 ***********************************************************/
var ChangeToDefault=function(){
    gg={};gg['fn']='ToCSed';gg['value']='default';
    $.post('sqlite_test.php',gg,function(d){
        if(d.valueCheck=='default'){    
            $('#selected_script div').html('default');
            getScriptBackO('floor','direction','function');   
        }else
            alert('script update error!!');
    },'json');  
};

/*****************************************************
 * function:
 *      the object to execute reflash the panel of options, 
 *      the panel means the dialog of "saveas" 
 * arguments:
 * 
 * return:
 * 
 *****************************************************/    

var reFlashOpe=function(opts){
    gg={};gg['fn']='getScript';
    $.post('sqlite_test.php',gg,function(d){
        
        if(opts.children()){    
            opts.children().remove();
            for (var i in d){
                opt=$('<option>').html(d[i]);
                opts.append($(opt));
            }   
        }
    },'json');
};

$(document).ready(function(){
    
    var formSaveAs=$("#SaveAsDia");
    var Opts=$('#fileOpt');
        
    formSaveAs.dialog({//buttons:[{text:'save',click:function(){console.info(save)}}],
        title:'Save As',autoOpen: false,dialogClass:'vboxDialogContent',
        resizeable:false,width:360,height:300,open:function(event,ui){
            //reflash the list of script could be selected<----!!!!!!
            reFlashOpe(Opts);
            
            
            
            gg={};gg['fn']='GetSSData';
            $.post('sqlite_test.php',gg,function(d){
                console.info(d.TTinital);
                //console.info($('#time_mode_dialog').data('tt'));
                $('#time_mode_dialog').data('tt',d.TTinital);
                if($('#time_mode_dialog').data('tt')=='normal'){
                    
                    $('#time_mode_dialog').html('(Mon to Fri)');    
                }else{
                    $('#time_mode_dialog').html('(weekend)');
                    
                }
                
                
            },'json');
        }});
    
    var SelFile=$('#fileOpt');
    var FileName=$('#filename');
    var BtnSave=$('input[type="button"][value="save"]');
    var BtnDel=$('input[type="button"][value="delete"]');
    var BtnCel=$('#celSaveAs');
    
    var dia_1=new FileDialog(SelFile,FileName,BtnSave,BtnDel,formSaveAs,BtnCel);
    dia_1.enSaveInter()//$('#time_mode_dialog').data('tt'));
    dia_1.enDelInter();
});

//this function is used to handle the dialog of function like saveas
function FileDialog(SelDom,NameDom,SaveDom,DelDom,form,BtnCel){
    var self=this;
    this.Sel=SelDom;
    this.Name=NameDom;
    this.Save=SaveDom;
    this.Del=DelDom;
    this.Cel=BtnCel;
    this.form=form;
    this.Cel.click(function(){form.dialog('close');});
    
    
    //let value of filename will change as option change
    this.Sel.data('refNameDom',this.Name).change(function(){
        $(this).data('refNameDom').val($(this).val());});
        
    var sel=this.Sel;
    
    self.enSaveInter=function(){    //register the function for the dom of "save"
        //below has put the ref of script name's dom into save's dom's data
        this.Save.data('refNameDom',this.Name).click(function(){
            console.info({action:'save',filename:$(this).data('refNameDom').val()});
            
            
            gg={};
            gg['fn']='ToCSed';//to change the script in selected
            var snObj=$(this).data('refNameDom');   //ref the object ref by data to snObj
            gg['value']=snObj.val();
            $.post('sqlite_test.php',gg,function(d){
                console.info(snObj.val());
                if(d.valueCheck==snObj.val()){//mean table selected's script name is changed right
                    //gg={};gg['fn']='updateNodeT';
                    var file=new saveAs();
                    file.collectPs();   //get all time
                    file.transmmit();
                    //below is to cahnge the box of display for selected script
                    $('#selected_script div').html(snObj.val());
                    //getScriptBackO($('#sel_1 :selected').text(),$('#sel_2 :selected').text(),$('#sel_3 :selected').text());    
                
                }else{
                    console.info('error on change script name of selected');
                }
            },'json');
            
                
            })
    };
    //method for delete the script    
    self.enDelInter=function(){ //register the click function for the dom of "delete"
        //$(this).data('refNameDom') present a dom object of text input of script name
        this.Del.data('refNameDom',this.Name).click(function(){
        	
            var scriptNAME=$(this).data('refNameDom').val();
            // console.info(scriptNAME);
            gg={};gg['fn']='getSELECTEDscript';
            $.post('sqlite_test.php',gg,function(d){
            	var Fstop=true;
            	$(d).each(function(key,value){
            		if(scriptNAME==d[key]){
            		    Fstop=false;
            		}
            	})
//             	if the script is in use, return will happen
            	if(Fstop==false){
            	    alert('你不能刪除已經被設定為使用中的腳本!');
            	    return;
            	}
            	if(scriptNAME=='default' || scriptNAME=='rest'){
                    alert('你不能刪除腳本default或rest!');
                    return;
                }else{
                	// console.info($(self));
                    var DelScName=scriptNAME;//$(this).data('refNameDom').val();
                    var file=new saveAs();
                    file.deleteS(DelScName,sel);                
                }
            },'json');
            
            
             
        })
    };
    
    
}





















