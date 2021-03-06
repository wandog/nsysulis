#!/usr/bin/env python 2.7
import Queue
import threading
#import sqlite3 as lite
import MySQLdb
import sys
import inspect
#from linuxserial import *
from linuxEth import *
import math
import time
from datetime import datetime
from time import gmtime, strftime
#from detectCHANGE import *

############################################################################
#function:
#    transfer the string of time to the unit of minutes in var type of INT
#arguments:
#    timestring: like "1:00", "17:00" and etc
#return:
#    an integer of minutes
############################################################################
def S2min(timestring):
    time=timestring.split(":")
    return int(time[0])*60+int(time[1])


############################################################################
#function:
#        judge present time belong to noraml or weekend
#arguments:
#        none
#return:
#        the string of time_type, ex:normal or weekend
############################################################################
def getTIMETYPE():
    typeSTRING=time.strftime('%a',time.localtime())
    return typeSTRING
#    if typeSTRING=='Sun' or typeSTRING=='Sat':
#        return  'weekend'
#    else:
#        return  'normal'

#function:!!!!


def scheduleSCRIPT(SorTT,typeSTRING):  #SorTT: return script or time_type/1:script 0:time_type
    
    con=MySQLdb.connect(passwd="q0919155809",db="powercontrol")
    with con:
        presentDATE=time.strftime('%Y-%m-%d',time.localtime())  #YEAR MONTH DAY
        typeSTRING=time.strftime('%a',time.localtime())     
#        print presentDATE
        cur=con.cursor()
#        cur.execute("select * from scheduleSPECI where dateS<='"+presentDATE+"' and dateE>='"+presentDATE+"'and (PERIODname<>'inLEARNING' AND PERIODname<>'summervacation' AND PERIODname<>'wintervacation');")
        cur.execute("select PERIODname,dateS,dateE,script from scheduleSPECI where dateS<='"+presentDATE+"' and dateE>='"+presentDATE+"';")
        row=cur.fetchone()
        if row:
#            print row[0],row[1],row[2],row[3],"\n"
#             PERIODname,datES,dateE,script
            if(SorTT==1):   #script    
                return row[3]
            else:           #time_type
                if typeSTRING=='Sat' or typeSTRING=='Sun':
                    return  'weekend'
                else:
                    return  'normal'
        else:   #wintervacation or summervacation or inLEARNING
            
            cur.execute("select * from (select * from scheduleSCRIPT where PERIODname='summervacation') as tt where dateS<='"+presentDATE+"' and dateE>='"+presentDATE+"';")
            row=cur.fetchone()
            if row: #in summervacation time
                if(SorTT==1):
                    if typeSTRING=='Sun':
                        return "rest"
                    else:
                        return row[3]
                else:
                    if typeSTRING=='Sat' or typeSTRING=='Sun':
                        return  'weekend'
                    else:
                        return  'normal'   
            else:   #in wintervacation time or inLEARNING time
                cur.execute("select * from (select * from scheduleSCRIPT where PERIODname='wintervacation') as tt where dateS<='"+presentDATE+"' and dateE>='"+presentDATE+"';")
                row=cur.fetchone()
                if row:#in summervacation time
                    if(SorTT==1):
                        if typeSTRING=='Sun':
                            return "rest"
                        else:
                            return row[3]
                    else:
                        if typeSTRING=='Sat' or typeSTRING=='Sun':
                            return  'weekend'
                        else:
                            return  'normal'
                else:   #inLEARNING time
                    cur.execute("select * from scheduleSCRIPT where PERIODname='inLEARNING'")
                    row=cur.fetchone()
                    if row:  #inLEARNING
                        if(SorTT==1):
                            return row[3]
                        else:
                            if typeSTRING=='Sat' or typeSTRING=='Sun':
                                return  'weekend'
                            else:
                                return  'normal'
                    else:
                        if(SorTT==1):
                            return "default"
                        else:
                            if typeSTRING=='Sat' or typeSTRING=='Sun':
                                return  'weekend'
                            else:
                                return  'normal'

            
        
        



#    benchMark = datetime.strptime('20110701', "%Y%m%d")
#    aa=datetime.strptime('2011-07-01', "%Y-%m-%d")
#    if(benchMark.date()>aa.date()):
#        print "big"
#    else:
#        print "less"
    




#############################################################################    
#function:
#        reflash the table of presentscript
#argument:    none
#return:    none
#############################################################################
def flashPreSCRI():
    con=MySQLdb.connect(passwd="q0919155809",db="powercontrol",charset='utf8')
    with con:
        cur=con.cursor()
        cur.execute("select mode from selected")
        modeType=cur.fetchone()
        modeType=modeType[0]         
#    if(modeType=='auto'):
        
        nowTIME=S2min((str(datetime.now()).split(" "))[1])
#    with con:
        curON=con.cursor()
        
        #print "select controlpoint,time from scripts where scripts.script in (select selected.script from selected) and scripts.switch='on' and scripts.time_type='",getTIMETYPE(),"' order by controlpoint;"
#        curON.execute("select controlpoint,time from scripts where scripts.script in (select selected.script from selected) and scripts.switch='on' and scripts.time_type='"+getTIMETYPE()+"' order by controlpoint;")
        curON.execute("select controlpoint,time from scripts where scripts.script='"+scheduleSCRIPT(1,getTIMETYPE())+"' and scripts.switch='on' and scripts.time_type='"+scheduleSCRIPT(0,getTIMETYPE())+"' order by controlpoint;")
#        print "select controlpoint,time from scripts where scripts.script='"+scheduleSCRIPT(1,getTIMETYPE())+"' and scripts.switch='on' and scripts.time_type='"+scheduleSCRIPT(0,getTIMETYPE())+"' order by controlpoint;" 
        curOFF=con.cursor()
    
        curOFF.execute("select controlpoint,time from scripts where scripts.script='"+scheduleSCRIPT(1,getTIMETYPE())+"' and scripts.switch='off' and scripts.time_type='"+scheduleSCRIPT(0,getTIMETYPE())+"' order by controlpoint;")
        print scheduleSCRIPT(1,getTIMETYPE()),scheduleSCRIPT(0,getTIMETYPE())
        
        rowON=curON.fetchone()
        rowOFF=curOFF.fetchone()
        
        if(modeType=='auto'):
            
            while(rowON and rowOFF):
    #            print "auto"
    #            print rowON[0]," ",rowON[1],"\n"
                onTIME=S2min(rowON[1])
                offTIME=S2min(rowOFF[1])
    #            print onTIME,"\n"
    #            print rowOFF[0]," ",rowOFF[1],"\n"
    #            print nowTIME," ",offTIME," ",onTIME,"\n"
            
                with con:
                    cur=con.cursor()
                    if((nowTIME<onTIME) or (nowTIME>offTIME) ):
                        cur.execute("update presentscript set shouldbe='0' where controlpoint='"+str(rowON[0])+"';")
                    else:
                        cur.execute("update presentscript set shouldbe='1' where controlpoint='"+str(rowON[0])+"';")
                
                rowON=curON.fetchone()      #for the while loop
                rowOFF=curOFF.fetchone()    #for the while loop
#        print getTIMETYPE(),"\n"
    return    


#################################################################    
#function: 
#    gen the byte data we need according some parameter
#argument:
#    originalData:the original byte data loaded from com port
#    bitpos:      the bit's position of targeg node
#    shouldbe:    the value wanted, ex true or false
#    sign:        the sign of this target, ex: it is 1 if the target relay is named "off"
#                                              it is 0 if the target relay is named "on" 
#################################################################     
def genByteFadspos(originalData,bitpos,shouldbe,sign):
    if sign==1: #handle the relay of "on"
        if shouldbe==1:  #set to 1
            
            oper1=1<<bitpos
            resultO=originalData|oper1
        else:   #set to 0 
            
            oper1=inverBit(1<<bitpos)
            
            resultO=originalData&oper1
    else:       #handle the relay of "off"
        if shouldbe==1:  #set to 0
            
            oper1=inverBit(1<<bitpos)
            
            resultO=originalData&oper1
        else:           #set to 1
            oper1=1<<bitpos
            print oper1
            resultO=originalData|oper1
    return resultO

#################################################################    
#argument:
#        hex:char of hex, ex:0~9 A~F
#return:
#        the number of hex, ex 0~15
#################################################################    

def hexChar2num(hex1):
    
    num=ord(hex1)
    #print num
    if(num<=57):   
        return num-48
    else:
        return num-55   
#################################################################    
#function:
#    get byte value of specifed address of PLC
#arguments:
#    Saddress: the address of target byte
#return:
#    the int value of target byte
#################################################################    
def readPLConeByte(Saddress):                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                              
    pre="\x02"
    station="00"
    WorR="51"
    startAddress=Saddress
    length="01"
    end="\x03"
    total=[station,WorR,startAddress,length,end]
    (check_1,check_0)=checkCodeCal(total)
    signalA=[pre,station,WorR,startAddress,length,end,check_1,check_0]
    signal=""
    for show_1 in signalA:
        signal=signal+show_1
    print signal
    #get the echo data of PLC
    dataR=sendCOMlinkR(signal)
    if(dataR!=''):
	print "get back data normally"
        print dataR, "len(dataR)=",len(dataR)
        #abstract the data from echo data
        temp_old2=""
        temp_old1=""
        temp_old0=""
        temp_new=""
        array_temp=[temp_old2,temp_old1,temp_old0]
        
        for chr_1 in dataR:
            temp_old2=temp_old1
            temp_old1=temp_old0
            temp_old0=temp_new
            temp_new=chr_1
            if temp_new=="\x03":
                break
    else:#nodata back and then the status should be set abnormal 1010 1010<==abnormal
	print "not get data normally"
        temp_old1="5"
        temp_old0="5"
        
    print temp_old1,temp_old0,"this is data read back\n"
    return 16*(hexChar2num(temp_old1))+hexChar2num(temp_old0)
#################################################################     
#function:
#    control one node's on or off according "shouldbe" in table 
#    of presentscript
#arguments:
#    the id of node, ex:a,b and etc
#return:
#   none 
#################################################################    
def WriteNODE(node):
    con=MySQLdb.connect(passwd="q0919155809",db="powercontrol",charset='utf8')
    with con:   #to make the access of dtabase could be finished normally 
                #even though exception happened
        cur=con.cursor()
        cur.execute("select node,address,bitpos,shouldbe,sign from mapPLC,presentscript where mapPLC.node=presentscript.controlpoint and node='"+node+"' order by sign")
        #\node\address\bitpos\shouldbe\sign
        rowOFF=cur.fetchone()
        rowON=cur.fetchone()
        print rowON[0],' ',rowON[1],' ',rowON[2],' ',rowON[3],' ',rowON[4]
        print rowOFF[0],' ',rowOFF[1],' ',rowOFF[2],' ',rowOFF[3],' ',rowOFF[4]
        if rowON[3]==1:#test the value of "shouldbe"
            #start turn on process of this node
            #turn off the "node's off relay"
            print rowOFF[1],"\n"
            value=readPLConeByte(rowOFF[1]) #read the original byte value of realy of turn off
            print value,"gogo\n"
            signalByte=genByteFadspos(value,rowOFF[2],rowOFF[3],0)
            a=signalByte/16
            b=signalByte%16
            
            print signalByte," ",time.time(),"\n"
            print int2ascii(a)," ",int2ascii(b),"\n data shoulde be writen in"
            print a," ",b,"data write in plc"
            PLCregiW(a,b,rowOFF[1])
            
    #        time.sleep(1)
            
            #turn on the "node's on relay"
            print rowON[1]
            value=readPLConeByte(rowON[1]) #read the original byte value of realy of turn off
            print value,"gogo\n"
            signalByte=genByteFadspos(value,rowON[2],rowON[3],1)
            a=signalByte/16
            b=signalByte%16
            
            print signalByte," ",time.time(),"\n"
            print int2ascii(a)," ",int2ascii(b),"\n data shoulde be writen in"
            print a," ",b
            PLCregiW(a,b,rowON[1])
            
            print 'on'
        else:#switch=off
            #start turn off process of this node
            #turn off the "node's on relay"
            print rowON[1]
            value=readPLConeByte(rowON[1]) #read the original byte value of realy of turn off
            print value,"gogo\n"
            signalByte=genByteFadspos(value,rowON[2],rowON[3],1)
            a=signalByte/16
            b=signalByte%16
            
            print signalByte," ",time.time(),"\n"
            print int2ascii(a)," ",int2ascii(b),"data shoulde be writen in\n"
            
            PLCregiW(a,b,rowON[1])
            
    #        time.sleep(1)
            
            #start turn on process of this node
            #turn on the "node's off relay"
            print rowOFF[1],"\n"
            value=readPLConeByte(rowOFF[1]) #read the original byte value of realy of turn off
            print value,"gogo\n"
            signalByte=genByteFadspos(value,rowOFF[2],rowOFF[3],0)
            a=signalByte/16
            b=signalByte%16
            
            print signalByte," ",time.time(),"\n"
            print int2ascii(a)," ",int2ascii(b),"data shoulde be writen in\n"
            
            PLCregiW(a,b,rowOFF[1])
            
            
            
            print 'off'
    return



#################################################################    
#function: read the real status of node back from the PLC
#arguments:
#        node:the char of the node
#return:
#        none
#################################################################
def ReadNODE(node):
    con=MySQLdb.connect(passwd="q0919155809",db="powercontrol",charset='utf8')
    with con:   #to make the access of dtabase could be finished normally 
                #even though exception happened
        cur=con.cursor()
        cur.execute("select node,address,bitpos,sign from mapPLC where node='"+node+"' order by sign")
        #\node\address\bitpos\shouldbe\sign
        rowOFF=cur.fetchone()
        
        rowON=cur.fetchone()
        print "read node test\n"    
        print rowON[0],' ',rowON[1],' ',rowON[2],' ',rowON[3]#,' ',rowON[4]
        print rowOFF[0],' ',rowOFF[1],' ',rowOFF[2],' ',rowOFF[3]#,' ',rowOFF[4]
        #\node[0]\address[1]\bitpos[2]\shouldbe[3]\sign[4]
        if(rowON[1]=='' or rowOFF[1]==''):  #no correct address return
	    active=-1
        else:
            byteRelayON=readPLConeByte(rowON[1])
            byteRelayOFF=readPLConeByte(rowOFF[1])
            print byteRelayON, byteRelayOFF,"the plc data read back\n" 
            
	    bitRelayON=(byteRelayON&(1<<rowON[2]))>>rowON[2]
            bitRelayOFF=(byteRelayOFF&(1<<rowOFF[2]))>>rowOFF[2]
            print bitRelayON,bitRelayOFF,"bit on and off\n"
            (status,active)=arithNODEinput(bitRelayON,bitRelayOFF)
            print status," ",active," this is read result"
            # the handle of error should be added
        
        
        lenS=len(node)#rowON[0])
        nodeNAME=node[:lenS-1]#rowON[0][:lenS-1]#to trim the last char 'n' in the nodeNAME
        cur.execute("update presentscript set itis="+str(active)+" where controlpoint='"+nodeNAME+"';")


#    active,nodeNAME,cur=readNODEsecONE(node)        
#    readNODEsecTWO(active,cur,nodeNAME)
        
        
        

#def readNODEsecONE(node):
#    con=MySQLdb.connect(passwd="q0919155809",db="powercontrol")
#    with con:   #to make the access of dtabase could be finished normally 
#                #even though exception happened
#        cur=con.cursor()
#        
##        cur.execute("select node,address,bitpos,shouldbe,sign from mapPLC,presentscript where mapPLC.node=presentscript.controlpoint and node='"+node+"' order by sign")
#        cur.execute("select node,address,bitpos,sign from mapPLC where node='"+node+"' order by sign")
#        #\node\address\bitpos\shouldbe\sign
#        rowOFF=cur.fetchone()
#        
#        rowON=cur.fetchone()
#        
#        print rowON[0],' ',rowON[1],' ',rowON[2],' ',rowON[3]#,' ',rowON[4]
#        print rowOFF[0],' ',rowOFF[1],' ',rowOFF[2],' ',rowOFF[3]#,' ',rowOFF[4]
#        #\node[0]\address[1]\bitpos[2]\shouldbe[3]\sign[4]
#        if(rowON[1]=='' or rowOFF[1]==''):  #no correct address return
#            active=-1
#        else:
#            byteRelayON=readPLConeByte(rowON[1])
#            byteRelayOFF=readPLConeByte(rowOFF[1])
#            bitRelayON=(byteRelayON&(1<<rowON[2]))>>rowON[2]
#            bitRelayOFF=(byteRelayOFF&(1<<rowOFF[2]))>>rowOFF[2]
#            
#            print bitRelayON,bitRelayOFF,"\n"
#            (status,active)=arithNODEinput(bitRelayON,bitRelayOFF)
#            print status," ",active," this is read result"
#            # the handle of error should be added
##    readNODEsecTWO(active,cur,nodeNAME)        
#    lenS=len(node)#rowON[0])       
#    nodeNAME=node[:lenS-1]#rowON[0][:lenS-1]#to trim the last char 'n' in the nodeNAME
#    return active,nodeNAME,cur        
#
#
#def readNODEsecTWO(active,cur,nodeNAME): 
#    cur.execute("update presentscript set itis="+str(active)+" where controlpoint='"+nodeNAME+"';")

            
#############################################	
#function: 
#    gen the string of controlnode
#arguments:
#    none
#return:
#     
#############################################   
def stringGEN():
	nodesWRITE=[]
	nodesREAD=[]
	con=MySQLdb.connect(passwd="q0919155809",db="powercontrol",charset='utf8')
	with con:	
		cur=con.cursor()
		cur.execute("select controlpoint from presentscript order by controlpoint;")
		rows=cur.fetchall()
    
		for row in rows:
			
			nodesWRITE.append(row[0])
			nodesREAD.append(row[0]+"n")
		
	return nodesWRITE, nodesREAD








    
   



    
