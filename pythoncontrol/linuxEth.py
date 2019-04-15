#!/usr/bin/env python 2.7
import time
import sys
import serial
import socket
def add_hex(hex1,hex2):
    return hex(int(hex1,16)+int(hex2,16))

def int2ascii(hexNum):
    if(hexNum<10):#0~9
        #print 48+hexNum,"this is ascii num\n"
        return chr(48+hexNum)
    else:#a~f
        #print 65+(hexNum-10),"this is ascii num\n"
        return chr(65+(hexNum-10)) 


        
#ser = serial.Serial(0,19200,7,parity=serial.PARITY_EVEN,timeout=1)  # open first serial port       
def sendCOMlink(signal):
#    ser = serial.Serial(0,19200,7,parity=serial.PARITY_EVEN,timeout=0.5)  # open first serial port
#    print ser.portstr       # check which port was really used
    client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    client_socket.connect(("140.117.120.110", 1000))	
    client_socket.send(signal)	
         # write a string
    time.sleep(0.1)
    client_socket.recv(10)
    client_socket.close()
    
    
def sendCOMlinkR(signal):
    #ser = serial.Serial(0,19200,7,parity=serial.PARITY_EVEN,timeout=1)  # open first serial port
    #print ser.portstr       # check which port was really used
          # write a string
    client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    client_socket.connect(("140.117.120.110", 1000))
    client_socket.send(signal)
         # write a string
    time.sleep(0.1)
    data=""
    
    data=client_socket.recv(12)
    
    client_socket.close()
    return data

def PLCregiW(h,l,address):
    #constant list for computer link data transfer
    pre="\x02"  #prefix signal
    station="00"    #station
    WorR="61"       #write mode
    startAddress=address
    length="01"     # length of 1 byte
    #01010101
    
    data=int2ascii(h)+int2ascii(l)
    #print data
    end="\x03"
    
    total=[station,WorR,startAddress,length,data,end]
    (check_1,check_0)=checkCodeCal(total)
    
    signalA=[pre,station,WorR,startAddress,length,data,end,check_1,check_0]
    
    signal=""
    
    #gen entire signal string
    for show_1 in signalA:
        signal=signal+show_1
    print signal
    sendCOMlink(signal)    
#function: convert the string of a byte to the upper part and lower part in decimal
#argument: 
#        str1:the string of 1 byte ex."01010101"
#return:
#        hex1:the upper part decimal
#        hex2:the lower part decimal

def str2hex(str1):
    i=7
    hex1=0
    hex2=0
    for chr_1 in str1:
        print chr_1,"\n"
        #print int(chr_1)
        if(i>3):
            hex1=hex1+int(chr_1)*int(math.pow(2,i-4))       
        else:
            hex2=hex2+int(chr_1)*int(math.pow(2,i))
        i=i-1
    return (hex1,hex2)    
    
    
#function: 
#    get the check code for PLC
#argument:
#    
def checkCodeCal(total):
    sum=hex(0)
    for word in total:
        for char_1 in word:
            sum=add_hex(sum,char_1.encode('hex'))
    #print sum 
    check_0=int2ascii(int(sum,16)%16)
    check_1=int2ascii((int(sum,16)%256)/16)
    return (check_1,check_0)
#######################################
#function:
#        turn byte like "0101" to "1010"
#argument:
#        the int you want to reverse
#return:
#        the reversed int
#######################################
def inverBit(a):
    return ~a&0xff


#############################################################
#function:
#    handle the arithmetic issue of relays for state reading
#    the data read back from mapPLC sould be handled by this funcation
#    beefore sent to the table of "presentscript"
#arguments:
#    a:    the value of status of realy "on"
#    aba:  the value of status of relay "off"
#return:
#    status:    normal or error
#    active:    on or off
#############################################################
def arithNODEinput(a,aba):
    a=int(a)
    aba=int(aba)
    if  a==aba:
#        return ('normal',a*(inverBit(aba)-254))
        return ('normal',a) #the x if plc read back when a node is on or off is 11 or 00
                            #not 10 or 01 as I thought before~
    else:
        
        return ('error',-1)
    
#(a,b)=arithNODEinput(0,1) 
#
#print a,'\n',b
#
#print 1<<2
    
    


    
