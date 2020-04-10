uuid = argv[1];

this_sess = freeswitch.Session(uuid)
this_sess:answer()

stream:write("Answered uuid "..uuid)