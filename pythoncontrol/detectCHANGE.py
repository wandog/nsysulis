#!/usr/bin/env python 2.7
from sqlite_control import *
import random


###############################################################
#function:
#    set the bit assigned in a byte to zero
#arguments:
#    orginalVAL: the value of specified address of plc's memory
#    bitPOS:     the position of specified bit in a byte
#return:
#    none
################################################################
def genVALz(orginalVAL,bitPOS):
    a=1<<bitPOS
    return orginalVAL&inverBit(a)
    
######################################################    
#function:
#    let the relays realted to input node set to zero
#argument:
#    the name of node
#return:
#    none
######################################################
def return2z(node):
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

        if(rowON[3]==0):#state is off and should turn relay from 01 to 00
            print rowOFF[1],"\n"
            value=readPLConeByte(rowOFF[1]) #read the original byte value of realy of turn off
            print value,"gogo\n"
            signalByte=genVALz(value,rowOFF[2])
            a=signalByte/16
            b=signalByte%16
            
            print signalByte," ",time.time(),"\n"
            print int2ascii(a)," ",int2ascii(b),"\n data shoulde be writen in"
            print a," ",b
            PLCregiW(a,b,rowOFF[1])
        else:           #state is on and should turn relay from 01 to 00
            print rowON[1],"\n"
            value=readPLConeByte(rowON[1]) #read the original byte value of realy of turn off
            print value,"gogo\n"
            signalByte=genVALz(value,rowON[2])
            a=signalByte/16
            b=signalByte%16
            print signalByte," ",time.time(),"\n"
            print int2ascii(a)," ",int2ascii(b),"\n data shoulde be writen in"
            print a," ",b
            PLCregiW(a,b,rowON[1])
    return    

#def stateCHECK(nodeN):
#    active,nodeNAME,cur=readNODEsecONE(nodeN)
#    if active!=-1:  #normal
#        return 
#    else:           #abnormal

###############################################
#function:
#    the control of plc's write and read
#argument:
#    the array of node's name
#return:
#    none
################################################ 
def PLCcontrol():
    lock=threading.Lock()
    con=MySQLdb.connect(passwd="q0919155809",db="powercontrol",charset='utf8')
    with con:
        cur=con.cursor()
        lock.acquire()
        cur.execute("select * from executeFLASH order by timeS;")
        row=cur.fetchone()
        
        if row:
            WriteNODE(row[0])
            #wait for a time to let relay stable
            time.sleep(0.9)
            ReadNODE(row[0]+"n")
            return2z(row[0])
            cur.execute("delete from executeFLASH where node='"+row[0]+"';")
            lock.release()
            print "execute!\n"
            
            #del the node from changed list after has send command to plc to change it
            
            return
#        else:
#
#        
#            return
######################################################            
#    function: 
#        to check every node's status randomly
#    arguments:
#
#    return:
######################################################
def SEQcheck(offset_R):
    con=MySQLdb.connect(passwd="q0919155809",db="powercontrol",charset='utf8')
	
    with con:
        cur=con.cursor()
#           
#        cur.execute("select controlpoint from controlnode order by rand() limit 1;")
#        cur.execute("select count(*) from controlnode;")
#        result=cur.fetchone()
		
#        for num in range(1,result[0]+1) :
#
#            print num
        cur.execute("select controlpoint from controlnode limit 1 offset "+str(int(offset_R)-1)+";")
        print "select controlpoint from controlnode limit 1 offset "+str(int(offset_R)-1)+";"
        row=cur.fetchone()
        cur.execute("insert into executeFLASH(node,timeS) values('"+row[0]+"',now());")
        print  "insert into executeFLASH(node,timeS) values('"+row[0]+"',now());"
            
    
#########################################################            
#function:
#    get the total number of records in controlnode
#return:
#    the number of counr
#########################################################
def getCOUNT():
    con=MySQLdb.connect(passwd="q0919155809",db="powercontrol",charset='utf8')
    
    with con:
        cur=con.cursor()
#           
#        cur.execute("select controlpoint from controlnode order by rand() limit 1;")
        cur.execute("select count(*) from controlnode;")
        result=cur.fetchone()
        return result[0]


def SEQexecute():
    countNUMBER=getCOUNT()
    print countNUMBER
    while 1:
        for i in range(1,countNUMBER+1):
            SEQcheck(i)
            time.sleep(10)

    
def PLCexecute():
    while 1:
        PLCcontrol()
   
########################################################   
#    handle the node having abnormal status
#    put this node into executeFLAH in a proper period
########################################################
def putABinFLASH():
    return
    while 1:
        con=MySQLdb.connect(passwd="q0919155809",db="powercontrol",charset='utf8')
        with con:
            cur=con.cursor()
            cur.execute("select controlpoint from presentscript where itis=-1;")
            rows=cur.fetchall()
            for row in rows:
                print row[0]
                cur.execute("insert into executeFLASH(node,timeS) values('"+row[0]+"',now())")
        time.sleep(10)


class OBJthread(threading.Thread):
    def __init__(self,queue):
        threading.Thread.__init__(self)
        self.queue=queue
        
        
    def run(self):

        func,arg=self.queue.get()
        if func!=None:
            
            print inspect.getargspec(func)
            print inspect.getargspec(func)[0]
             
            if len(inspect.getargspec(func)[0])!=0:#check if this func need arguments
                print "argumeents needed"
                func(arg)    
            else:
                print "no arguments"
                func()
    
            self.queue.task_done()
            time.sleep(0.5)
            self.queue.empty()

def FLASH():
    while 1:
        flashPreSCRI()
        time.sleep(1)

    
def main():
    #below part is about the multi-thread
    #print "execute"    
    q=Queue.Queue()
    q1=Queue.Queue()
    q2=Queue.Queue()
    q3=Queue.Queue()
    
#    thread for reflash presentscript according to the selected script and present time
    tFLASH=OBJthread(q)
    tFLASH.setDaemon(True)
    tFLASH.start()
    
#    thread for detect abnormal node
    tDETECTab=OBJthread(q2)
    tDETECTab.setDaemon(True)
    tDETECTab.start()
    
    tPLC=OBJthread(q1)
    tPLC.setDaemon(True)
    tPLC.start()
#    thread for check the realy set the relay state randomly
    tRAND=OBJthread(q3)
    tRAND.setDaemon(True)
    tRAND.start()
    
    q.put((FLASH,"none"))
    q1.put((PLCexecute,"hello"))
    q2.put((putABinFLASH,"hello"))
    q3.put((SEQexecute,"hello"))
    while(1):
       time.sleep(10)


main()


    



