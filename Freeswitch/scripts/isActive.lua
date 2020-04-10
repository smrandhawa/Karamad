uuid = argv[1];

this_sess = freeswitch.Session(uuid)

--file = io.open("logtest123.txt", "a+")
-- this_sess:ready() 
if this_sess:ready() then
	--file:write("Call Status: Connected.\n")
	-- freeswitch.consoleLog("info", "Connected")
	stream:write(" true")
else
	--file:write("Call Status: Disconnected.\n")
	-- freeswitch.consoleLog("info", "Disconnected")
	stream:write(" false")
end

--file:close()