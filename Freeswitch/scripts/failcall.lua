
callerid = argv[13];
phno = argv[1];
deployment=argv[2]
calltype=argv[3]
To_Whom=argv[4]
oreqid=argv[5]
recIDtoPlay=argv[6]
Effect_Chosen=argv[7]
ocallid=argv[8]
ouserid=argv[9]
testcall=argv[10]
ch=argv[11]
app=argv[12]
From=argv[14]

api = freeswitch.API()

hcause = "" 
freeswitch.consoleLog("INFO","caller:  " .. phno .. "--"..To_Whom.."\n")
freeswitch.consoleLog("INFO","callerid passing throw dialplan:  " .. callerid .. "\n")

freeswitch.consoleLog("info", " Call.lua started")

web_url = "http://127.0.0.1/FS/APollyLHR.php?error=".."TypeError".."&deployment="..deployment.."&calltype="..calltype.."&phno="..To_Whom.."&oreqid="..oreqid.."&recIDtoPlay="..recIDtoPlay.."&effectno="..Effect_Chosen.."&ocallid="..ocallid.."&ouserid="..ouserid.."&testcall="..testcall.."&ch="..ch.."&app="..app	 	
raw_data = api:execute("curl", web_url) 