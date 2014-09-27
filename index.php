<!DOCTYPE HTML>

<title>LinOrA <?php echo $vsn;?> - Link Organizer and Archiver</title>

<meta name="description" content="LinOrA - an Organizer and Archiver for Web-links"/>
<meta name="author" content="Kit Lester"/>
<meta name="keywords" content="Web-link, Organizer, Archiver"/>
<meta charset=UTF-8 />

<link rel="shortcut icon" href="favicon.ico"/>
<link type="text/css" rel="stylesheet" href="LIB/linora.css" media="all">

<script src="LIB/(string.uriencodes).js"></script>
<script src="LIB/(ajax.get).js"></script>
<script src="LIB/(ajax.post).js"></script>
<script src="LIB/linora.js"></script>


<div class="header">
    <div class="title"><big>&nbsp;<big><big><b>Linora</b>&nbsp;&nbsp;&nbsp;&nbsp;
			</big></big></big></div>
    <div class="shows">Show&hellip;&nbsp;
	    <input type="checkbox" id="helpflip"/>help&nbsp;
	    <input type="checkbox" id="addflip"/>add&nbsp;
	    <input type="checkbox" id="linksflip"/>links
    </div>
</div>


<div class="hack"><p>&nbsp;<br>&nbsp;</p><div>

<fieldset id="add" style="display:none">
    <legend><b>Link to be added</b></legend>

    <div id="badInsertDiv" style="color:red"></div>

    <form id="postage">
	<table cellspacing="2">
	<tr><td>Category:&nbsp;
	    <td><input name="cat" type="text"
				  size="40" maxlength="100" />
		</td></tr>
    	<tr><td>URL:&nbsp;
	    <td><input name="url" type="text"
				  size="40" maxlength="200" />
		</td></tr>
    	<tr><td>Caption:&nbsp;
	    <td><input name="cap" type="text"
				  size="40" maxlength="300" />
		</td></tr>
	<tr><td><input type="submit" value="Add"></td></tr>

    	</table>
    </form>
</fieldset>


<fieldset id="links" style="display:none">
    <legend><b>Archived Links</b></legend>
    <div id="linksDiv"></div>
</fieldset>

<fieldset id="help" style="display:none">
    <legend><b>Help etc.</b></legend>
	<div id="helpDiv"></div>
</fieldset>

