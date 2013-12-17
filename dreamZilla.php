 <?php
if ($_POST) {
    $key = $_POST['apikey'];
    $uuid = md5(uniqid(rand(), true)); //Create unique ID to prevent triggering abuse/flood mechanisms
    $url = 'https://api.dreamhost.com/?key=' . $key . '&cmd=user-list_users&unique_id=' . $uuid . '&format=xml';
    $contents = file_get_contents($url); //Load API call
    $error = "<span>Processing Error.</span><br /><a href='./'>Try again</a>";
    if (strstr($contents, "<result>success</result>") == FALSE) { die($error); } //Check to make sure everything was processed correctly on their end.
    $contents = str_replace("<result>success</result>", "", $contents); //Remove the excess data...
    $contents = trim($contents); //Loose some weight...
    $contents = '<?xml version="1.0" encoding="UTF-8"?><rss version="0.92">' . $contents . '</rss>'; //Wrap it up...
    $rss = simplexml_load_string($contents); //Reprocess into Filezilla formatting...
    $newcontent = '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?><FileZilla3><Servers><Folder expanded="1">My DreamHost Sites';
    foreach ($rss->dreamhost->data as $item) {
        $type = "0";
        if (($item->type == 'ftp') || ($item->type == 'shell') || ($item->type == 'sftp')) {
        if (($item->type == 'sftp') || ($item->type == 'shell')) { $host = $item->home; $prot = "1"; $port = "22"; } else { $prot = "0"; $host = $item->home; $port = "21";}
        $newcontent .= '<Server>
                <Host>' . $host . '</Host>
                <Port>' . $port . '</Port>
                <Protocol>' . $prot . '</Protocol>
                <Type>0</Type>
                <User>' . $item->username . '</User>
                <Pass></Pass>
                <Logontype>2</Logontype>
                <TimezoneOffset>0</TimezoneOffset>
                <PasvMode>MODE_DEFAULT</PasvMode>
                <MaximumMultipleConnections>0</MaximumMultipleConnections>
                <EncodingType>Auto</EncodingType>
                <strongypassProxy>0</strongypassProxy>
                <Name>' . $item->gecos . ' - ' . $item->username . '</Name>
                <Comments></Comments>
                <LocalDir></LocalDir>
                <RemoteDir></RemoteDir>
                <SyncBrowsing>0</SyncBrowsing>' . $item->gecos . '
            </Server>';
        }
    }
    $newcontent .= '</Folder></Servers></FileZilla3>';
    header('Content-disposition: attachment; filename=sites.xml');
    header('Content-type: application/xml');
    echo $newcontent;
    exit();
}
?>
<!DOCTYPE HTML>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <title>DreamZilla</title>
    <style type="text/css">
    body {
        background-color:#101115;
        margin:0px;
        padding:0px;
        color:#91C8FF;
        font-family:"Lucida Sans Unicode","Lucida Console",Arial,Georgia,Helvetica,sans-serif;
        border-top:10px solid #FFF;
        font-size:12px;
        text-align:left;
    }
    ol {
        margin:0;padding:0;
        float:left;
    }
    ol li {
        font-size:14px;
        color:#FFFFFF;
        margin:0px 13px 0px 33px;
    }
    input[type="text"] {
        border:none;
        border-bottom:1px solid #FFFFFF;
        background:transparent;
        color:#FFFFFF;
        padding:3px;
        width:175px;
    }
    input[type="submit"] {
        border:1px solid #FFF;
        background:#333333;
        color:#FFFFFF;
        margin:0 10px;
    }
    h1 {
        color:#FFFFFF;
        border-bottom:1px solid;
        font-style:italic;
        font-weight:normal;
        margin:15px 0px 0px;
        text-align:left;
        font-size:20px;
    }
    h2 {
        color:#FFFFFF;
        border-bottom:1px solid;
        font-style:italic;
        font-weight:normal;
        margin:0 auto;
        text-align:left;
        font-size:16px;
        width:95%;
    }
    h1 span {
        font-size:12px;
        float:right;
        font-style:normal;
    }
    h1 span a {
        border-bottom:1px solid #91C8FF !important;
        position:relative;
        top:7px;
        color:#91C8FF !important;
    }
    .container {
        width:600px;
        margin:50px auto 0px;
    }
    .container h3 {
        font-size:24px;
        font-weight:normal;
        margin:37px 7px;
    }
    .container h3 span {
        color:#FFFFFF;
    }
    .content {
        margin:30px 10px 0px;
    }
    .nav {
        margin-left:10px;
    }
    .nav ul {
        list-style-type:none;
        display:block;
        padding:0px;
        margin:0px;
    }
    .nav ul li {
        text-align:left;
        float:left;
    }
    .nav ul li a {
        padding:3px 20px;
        color:#FFF;
        text-decoration:none;
        border-left:1px solid #CCCCCC;
    }
    .nav ul li a:last-child {
        border-right:1px solid #CCCCCC;
    }
    .nav ul li a:hover {
        background-color:#FFFFFF;
        color:#000000;
    }
    .clear {
        clear:both;
        height:0px;
    }
    .selected {
        background-color:#FFFFFF;
        color:#000000 !important;
    }
    .content a {
        color:#FFFFFF;
        font-weight:bold;
        text-decoration:none;
        border-bottom:1px dashed;
    }
    .desc {
        font-size:14px;
        color:#91C8FF;
    }
    .footer {
        padding-top:10px;
    }
    .forealfooter {
        text-align:center;
        font-size:10px;
        border-top:1px solid;
        padding-top:15px;
    }
    #use p {
        margin:10px;
    }
    #formdiv {
        float:right;
        padding:5px 0 10px;
        width:335px;
        border-left:1px dotted #FFFFFF;
    }
    #formdiv a {
        position:relative;
        left:100px;
        top:50px;
    }
    #formdiv span {
        position:relative;
        left:85px;
        top:50px;
    }
    #about span, #faq span, #source span, #source strong, #about strong, #about em {
        color:#FFFFFF;
    }
    #faq ul {
        margin:0 30px 0;
        padding:0px;
        list-style-type:upper-roman;
    }
    #faq li {
        margin-bottom:10px;
    }
    </style>
</head>

<body id="top">
<div class="container" id="tabs">
    <h3>Dream<span>Zilla</span><span class="desc"> - FileZilla SiteManger XML Generator for Dreamhost</span></h3>
    <div class="nav">
        <ul class="idTabs">
        <li><a href="#about">About</a></li>
        <li><a href="#use">Use DreamZilla</a></li>
        <li><a href="#source">Source</a></li>
        <li><a href="#faq">FAQ</a></li>
        <br class="clear" />
        </ul>
    </div>
    <div class="content tabContainer" id="content">
        <div id="about">
            <h1>About</h1><br />
                <strong>Hate</strong> having to manually log in to upload a new revision of your site?  <em>Too many</em> sites to manage?<br />
                You and about <strong><em>OVER 9000</em></strong> other Happy DreamHost customers.<br />
                Life's about making things <em>simple.</em>  That's exactly what <span>DreamZilla</span> does.<br />
                Login, download, import.  <span>Simple.</span><br />
        </div>
        <div id="use">
            <h1>Use DreamZilla</h1><br />
            <div id="formdiv">
                <form action="" method="post">
                    <h2>Generate</h2>
                    <p><label for="apikey">DreamHost API Key:</label><input type="text" name="apikey" id="apikey" /></p>
                    <input type="submit" id="submitAPI" value="Download" />
                </form>
            </div>
            <ol>
                <li>Generate an API key <a href="https://panel.dreamhost.com/index.cgi?tree=home.api&">here</a></li>
                <li>Enter that API key &rarr;</li>
                <li>Download the given XML file</li>
                <li>Import to Filezilla!</li>
            </ol>
            <br class="clear" />           
        </div>
        <div id="source">
            <h1>Source</h1><br />
            DreamZilla is licensed under GPL I suppose.  I'm not one to really care about licensing in regards to applications of this nature.  Do with it what you'd like, but I don't mind credit.<br />
            Here's the download link to the source code.  Includes everything that the live version is running.<br /><br />
            <span><strong>Current:</strong></span><br />
            <div><a href="/dropBox/src/dreamzilla-0.2.zip">DreamZilla 0.2</a></div>
            <br class="clear" /><br />
            <span>Previous Versions:</span><br />
            <strong>Dreamzilla 0.1</strong> Removed and updated to a smaller, more streamlined script.  Old version also included password output which is now useless.
        </div>
        <div id="faq">
            <h1>FAQ</h1><br />
            <ul>
                <li><span>What does DreamZilla do?</span><br />
                    It gives you another reason to be lazy.<br />
                    DreamHost account holders can come here, login with their API key, this application will spit out a formatted XML file containing the login information for their Users which can be imported into FileZilla.
                </li>
                <li><span>How do I get the XML file to work with FileZilla?</span><br />
                    It depends on which version you're running.<br />
                    Open the <span>File</span> menu and click on <span>Import...</span>.<br />
                    If you can't find the <span>Import...</span> option under the <span>File</span> menu, then look under the <span>Edit</span> menu.<br />
                    From there locate the downloaded XML file and follow the default options.<br />Open your SiteManger and all the users should be loaded.
                </li>
                <li><span>Which versions of FileZilla are supported?</span><br />
                    Every version of FileZilla in the 3.x builds.  The only difference is the menu location for the <span>Import</span> button.
                </li>
                <li><span>Is DreamZilla Open-Source?</span><br />
                    Yes.<br />
                    You can find the download links under the <a href="#source">Source</a> section.
                </li>
                <li><span>Is this it?</span><br />
                    What more do you want it to do?
                </li>
            </ul>
        </div>
        <div class="footer">
            <div class="forealfooter">Written by <a href="http://www.kenmoini.com">Ken Moini</a><br />This web application is in no way affliated to <a href="http://www.dreamhost.com">DreamHost</a><br />DreamHost is not responsible for the script, statements, or errors shown here.<br />I only use their fandangled API as a base for a handy-dandy script.</div>
        </div>
    </div>
    <br class="clear" />
</div>
</body>
</html>
