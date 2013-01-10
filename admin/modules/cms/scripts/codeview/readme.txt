======================================================================================================================
INTRODUCTION
======================================================================================================================
Use this script to create a code viewer with realtime syntax hilighting. You can create as many viewers as you want
within your HTML page.

======================================================================================================================
LICENSE
======================================================================================================================
This script is freeware for non-commercial use. If you like it, please feel free to make a donation!
However, if you intend to use the script in a commercial project, please donate at least EUR 10.
You can make a donation on my website: http://www.gerd-tentler.de/tools/codeview/.

======================================================================================================================
USAGE
======================================================================================================================
Insert this script into your HTML page like this:

	<html>
	<head>
	...
	<script src="codeview.js" type="text/javascript"></script>
	...
	</head>
	<body>
	...

Then insert one or more PRE tags containing your code and use "codeview" followed by the script language as class
name:

	<pre class="codeview php" style="width:600px; height:300px;">...</pre>
	...
	<pre class="codeview javascript" style="width:600px; height:300px;">...</pre>
	...

Line numbers can be viewed by adding the option "lineNumbers":

	<pre class="codeview php lineNumbers" style="width:600px; height:300px;">...</pre>

======================================================================================================================
LIST OF SUPPORTED LANGUAGES
======================================================================================================================
Currently the code viewer supports the following languages:

PHP
Perl
JavaScript
HTML
CSS
XML
SQL

======================================================================================================================
Source code + examples available at http://www.gerd-tentler.de/tools/codeview/.
======================================================================================================================
