<!DOCTYPE html>
<html>
<head>
<?php include "synckit-header.php" ?>
<script type="text/sjs">
$(function() {
    // console.info("Page Loaded");
    window.synckit = create_synckit(); 
    var endpoint = "/blog/autosync";

    var viewspec = {
        "vshash": "3c9977be25383449119b351ce5388e81",
        "syncspec": {
             "__type": "sql",
             "query": "SELECT blog_entry.id, blog_entry.title, blog_entry.contents, blog_entry.date FROM blog_entry ORDER BY blog_entry.date DESC LIMIT 10;"
            }, 
        "schema": ["title varchar(200)", "date timestamp with time zone", "contents text", "id serial"]
    };

    window.synckit.build_view(endpoint, "blog_entry", viewspec);

    // 'now' is a parameter used for time-travel through the posts
    var extra_view_params = {};
    //var now = urlParam('now');
	/*
    if (now != 'undefined' && now != '0') {
	    extra_view_params.Posts = {"now": now};
	}
     */
    
    var callback = function() {
        window.synckit.timeStart("template");
		$('#newtemplate').render_new();
	    window.synckit._templateTime = window.synckit.timeEnd("template");;
        //if (parent.LogData != "undefined") {
        //	parent.LogData("Blog", "Auto", window.location.href, window.synckit);
        //}
    };

    window.synckit.sync(endpoint, ["blog_entry"], extra_view_params, {}, callback);
});

</script>
</head>
<body>  
    
<div id="loading">
    <button id="clearButton" onclick="window.synckit.reset();">Reset DB</button><button onclick="window.synckit.dump();">Dump DB</button>
</div>

<div id="debug">
</div>
<div id="templated">
<p align="center"><b>Template Loading...</b></p>
</div>

<table id="newtemplate" query="SELECT * From sk_blog_entry2 LIMIT 10;" as="entry">
	<tr itemscope="yes" itemtype="Entry" />
		<td><span itemprop="title">from</span></td>
		<td><span itemprop="contents">content</span></td>
	</tr>
</table>

</body>

</html>
