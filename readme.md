Rivendell HTML

** What is it? **

In simple terms, I'm trying to replace the non-critical parts of Rivendell
with HTML, Javascript and PHP.

Tested with Riv 2.10.3 (Tryphon Repos).  The service table was changed
after this version to pull the CLOCKS into a SVC_CLOCKS table.  This won't
work properly with newer versions.

** What do you mean non-critical? **

Anything that doesn't have to run in real time e.g. rdairplay for radio 
output.  This would also apply to rdpanels, caed, ripcd etc.

Basically most of the non-presenter using bits of Rivendell that don't
need to happen with low latency audio delays.

** Why? **

There have been murmurings of QT5 updates to Rivendell.  I think this is
quite a long way off as it looks like an immense amount of work.

If look at the source code (approx 200,000 lines):

```SLOC	Directory	SLOC-by-Language (Sorted)
62339   lib             cpp=62339
33880   *rdadmin*       cpp=33880
11563   rdairplay       cpp=11563
11028   utils           cpp=9741,ansic=1170,sh=117
10989   ripcd           cpp=10989
8317    *rdlogedit*     cpp=8317
7549    *rdlogmanager*  cpp=7549
7319    *rdlibrary*     cpp=7319
7206    rdcatch         cpp=7206
5841    cae             cpp=5841
3764    rdcatchd        cpp=3764
3494    rlm             ansic=3494
3461    web             cpp=3461
2836    rdhpi           cpp=2836
2002    importers       cpp=1977,sh=25
1582    tests           cpp=1582
1528    rdcastmanager   cpp=1528
869     rdrepld         cpp=869
677     helpers         sh=390,ansic=201,cpp=86
557     top_dir         sh=557
514     rdmonitor       cpp=514
435     debian          sh=435
431     scripts         sh=431
403     rdcartslots     cpp=403
392     ios             objc=392
378     rdpanel         cpp=378
307     rdselect        cpp=307
276     rdlogin         cpp=276
209     pam_rd          cpp=209```

*generated using David A. Wheeler's 'SLOCCount'.

Big portions of rdadmin, logedit, log manager and library could be entirely
replaced with web pages.  If we could take these off the todo list you can
save about 40,000 lines of code.  I haven't looked too clearly at the libs
directory so its possibly a lot more.  Either way if this was HTML/PHP etc,
this is at least 20% of all Riv development/conversion to QT5.

Obviously you could run through everything and get it done but:
* I'm not great with C++/QT so this is the best way I can try to help.
* A lot of Riv is quite a simple system of reading or changing database
  values (normal CRUD stuff).
* I forget where the quote came from but, "Web pages are just skins to
  databases" and it fits quite well.
* HTML/PHP/CSS/Javascript are much more accessible to more people so in
  theory the Rivendell guys will have more help if the tools are built
  in these languages.
* If you have a browser you can use the tools, you don't need a Windows,
  OSX, BSD or Linux build (or phones/tablets).
* I recently had to spend a lot of time with Rivendell events, grids and
  clocks.  They work really well but I could see some ways that would be
  simpler/faster to use, especially as someone new to Riv.
* The mailing lists occasionally talk about how other systems generate
  templates.  I think it will be easier to tweak/implement new behaviour
  for these tools in HTML etc than QT/C++ for most people.

** How long will this take? **

I have no idea, this is a spare time/boredom project.

** Anything else you would like to do? **

I don't know how feasible it is but if I can figure out a way of getting 
sound cloud style wav form elements in a web page it might even be 
possible to replace rdlibrary, voice tracking and potentially audio 
importing.

These are long term/will never be done goals but you never know.

** Why plain HTML/PHP etc? **

I have dabbled with various frameworks (most recently CakePHP, Ionic, 
Angular) over the years.  You tend to spend about as much time learning 
the framework as actually coding something.

Then after a while you realise there are big security vulnerabilities in 
the framework you used and it needs to be updated.  Then you realise the API
changed massively and everything is broken.

This is bad when you haven't necessarily touched the code for months/years.
All the parts of your code that you used to know are now a hazy blurry mess
of pain and misery so it takes 10 times longer to fix than it should.

So unless there is a good reason I'll try and stick to standard PHP and
Javascript rather than bolting on Angular, JQuery etc.  JQuery may be a 
necessity as pure Javascript GET and POSTS are quite a horrible convluted
mess and there are some nice JQuery date/time selectors.

** This code is rubbish! **

Most probably yes.  I have a background in business apps written in Java.
I taught myself PHP in an afternoon over 15 years ago.  I still have never
written any webpage that is complicated enough to bother with Object
Orientated design.

It is all just outputting strings of text and so far simple functions in
organised files that you can add with includes work for me.

If you can suggest a framework or way of organising things that makes sense
feel free to let me know.  If it seems like a good plan I'll refactor to
make it happen.

** How do I use it? **

Install MySQL/PHP/Apache (standard lamp stack) on a machine somewhere.  If
you have Rivendell, you already have everything but PHP on at least one
machine.

Download the code and drop it somewhere on your web server.

If the Rivendell db is on localhost, it will just work.  If not edit:
/config/database.php
