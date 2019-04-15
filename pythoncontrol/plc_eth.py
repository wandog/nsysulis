import time
import socket

def int2ascii(hexNum):
    if(hexNum<10):#0~9
        #print 48+hexNum,"this is ascii num\n"
        return chr(48+hexNum)
    else:#a~f
        #print 65+(hexNum-10),"this is ascii num\n"
        return chr(65+(hexNum-10)) 
def add_hex(hex1,hex2):
    return hex(int(hex1,16)+int(hex2,16))

def checkCodeCal(total):
    sum=hex(0)
    for word in total:
        for char_1 in word:
            sum=add_hex(sum,char_1.encode('hex'))
    #print sum 
    check_0=int2ascii(int(sum,16)%16)
    check_1=int2ascii((int(sum,16)%256)/16)
    return (check_1,check_0)

pre="\x02"
station="00"
WorR="51"
startAddress="00FD"
length="01"
end="\x03"
total=[station,WorR,startAddress,length,end]
(check_1,check_0)=checkCodeCal(total)
signalA=[pre,station,WorR,startAddress,length,end,check_1,check_0]
signal=""
for show_1 in signalA:
	signal=signal+show_1

print signal


    
	
client_socket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
client_socket.connect(("140.117.120.110", 1000))	
	
    
	
client_socket.send(signal)
time.sleep(1)
data = client_socket.recv(12)
print data
		
client_socket.close()
           