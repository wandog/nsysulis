/**
 * @author jhma
 * @date 2012/9/28
 * @email:jhma@staff.nsysu.edu.tw
 */
$(document).ready(function(){
    
    var sels=['sel_1','sel_2','sel_3'];
    //console.info(sels);
    
    var gg=new orderOpCrl(sels);
    gg.handleDOM();
});

 
//for control the options of order selection
function orderOpCrl(sels){
    var self=this;
    
    this.lenTar=sels.length;
    
    this.objsel=[];
    
    //put element into objsel array
    for(i=0;i<this.lenTar;i++){
        this.objsel[i]=$('#'+sels[i]);
    }
    //generate a link list for these elements
    for(i=0;i<this.lenTar;i++){
        if(i!=(this.lenTar)-1){
            this.objsel[i].data('next',this.objsel[i+1]);    
            
        }else{
            this.objsel[i].data('next',this.objsel[0]);
        }
    }
    
    
    
    self.handleDOM=function(){
        var i=0;
        while(i<this.lenTar-1){    
            this.objsel[i].change(function(){
                //console.info($(this).data('next'));        
                var select_id=$(this).attr('id');
                //$('#'+sel_id+' :selected');
                //var OptNext=[];
                var OptSel=$('#'+select_id+' :selected').val();//the option selected's value
                //console.info(OptSel);
                //console.info($('#'+sel_id).find('option :eq(2)'));
                itemLen=$('#'+select_id).find('option').length;
                //alert(itemLen);
                
                $(this).data('next').children().each(function(index,value){
                    if($(this).val()!='default')
                        $(this).remove();
                });
                
                var q=1;
                var id_next=$(this).data('next').attr('id');
                
                for(j=1;j<itemLen;j++){//itemLen表示所has的option個數
                    if($('#'+select_id+' option[value='+j+']').val()==OptSel){
                        //console.info('nothing');
                    }else{
                        //console.info($('#'+select_id+' option[value='+j+']'));
                        var OptText=$('#'+select_id+' option[value='+j+']').text();
                        var OptValue=q;
                        var option=$('<option>').text(OptText).val(OptValue);
                        
                        $(this).data('next').append(option);//point to next element
                        //the option should be packaged to next select
                        //console.info(option);
                        q++;
                    }           
                }
                
            });
            i++;  
        }    
    }
    
    
    
}

