pathsep = '\\'

sessionId = argv[1]
freeswitch.consoleLog("INFO","sessionId:  " .. sessionId .. "\n")
this_sess = freeswitch.Session(sessionId);
this_sess:answer();

api = freeswitch.API()
uuida = this_sess:get_uuid()

this_sess:setAutoHangup(false)
uuida1 = string.sub(uuida,1)

web_url = "http://127.0.0.1/FS/SurveyCallIn.php?uuid=" ..uuida1
freeswitch.consoleLog("INFO","New Survey URL:  " .. web_url .. "\n")

raw_data = api:execute("curl",web_url)
freeswitch.consoleLog("INFO","Raw data:\n" .. raw_data .. "\n\n")
