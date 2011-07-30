<?php

//--------------------------------------------------------------------------->
// 26-07-2011 | Andrew DUNBAR | Establish path.
//--------------------------------------------------------------------------->

$path = dirname(__FILE__)."/";

//--------------------------------------------------------------------------->
// 26-07-2011 | Andrew DUNBAR | Establish vars.
//--------------------------------------------------------------------------->

$pageOutput  = "";
$pageContent = "";
$returnData  = "";

//--------------------------------------------------------------------------->
// 26-07-2011 | Andrew DUNBAR | Establish SOAP details for Matrix WebCMS.
//--------------------------------------------------------------------------->

$cfg['MatrixSOAP']['user']     = 'WebSOAP';
$cfg['MatrixSOAP']['password'] = '';
$cfg['MatrixSOAP']['login']    = array('login' => $cfg['MatrixSOAP']['user'], 'password' => $cfg['MatrixSOAP']['password']);
$cfg['MatrixSOAP']['server']   = 'https://www.mydomain.com.au/_web_services/soap-server?WSDL';

//--------------------------------------------------------------------------->
// 26-07-2011 | Andrew DUNBAR | Set SOAP requests not to be cached.
//--------------------------------------------------------------------------->

ini_set("soap.wsdl_cache_enabled", "0");

//--------------------------------------------------------------------------->
// 26-07-2011 | Andrew DUNBAR | Establsih SOAP request.
//--------------------------------------------------------------------------->

try {

    $client = new SoapClient($cfg['MatrixSOAP']['server'], $cfg['MatrixSOAP']['login']);

    //--------------------------------------------------------------------------->
    // 26-07-2011 | Andrew DUNBAR | Get 'PageContents' from asset.
    //--------------------------------------------------------------------------->

    // See Squiz manual on 'PageContents' http://manuals.matrix.squizsuite.net/web-services/chapters/soap-api-asset-service/#cloneasset
    $serviceVars   = array('AssetID' => 'XXXXXXXX', 'NewParentID' => 'YYYYY', 'LinkType' => '2');
    $result        = $client->cloneAsset($serviceVars);
    $whichFunction = "cloneAsset";

    //--------------------------------------------------------------------------->
    // 26-07-2011 | Andrew DUNBAR | Was previous webservices call successful?
    //--------------------------------------------------------------------------->

    if ($result) {

        //--------------------------------------------------------------------------->
        // 26-07-2011 | Andrew DUNBAR | Return SOAP server keys and values.
        //--------------------------------------------------------------------------->

        foreach ($result as $resultKeys => $resultValues) {

            $pageContent .= "    <tr>\n";
            $pageContent .= "        <th valign=\"top\">".$resultKeys."</th>\n";
            $pageContent .= "        <td valign=\"top\">".$resultValues."</td>\n";
            $pageContent .= "    </tr>\n";

        }

    } else {
    
            $pageContent .= "    <tr>\n";
            $pageContent .= "        <th colspan=\"2\">Error: SOAP failed to execute correctly...</th>\n";
            $pageContent .= "    </tr>\n";
    }


} catch (SoapFault $e) {

    throw new Exception("SOAP Fault Encountered: ".$e->getMessage());

}

//--------------------------------------------------------------------------->
// 26-07-2011 | Andrew DUNBAR | Build up page content.
//--------------------------------------------------------------------------->

$pageOutput .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"\n";
$pageOutput .= "     \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
$pageOutput .= "<html xmlns=\"http://www.w3.org/1999/xhtml\">\n";
$pageOutput .= "<head>\n";
$pageOutput .= "<meta http-equiv=\"content-type\" content=\"text/html; charset=utf-8\" />\n";
$pageOutput .= "<title>Matrix Web Services Examples: ".$whichFunction."</title>\n";
$pageOutput .= "<style type=\"text/css\" title=\"text/css\" media=\"all\">\n";
$pageOutput .= "/* <![CDATA[ */\n";
$pageOutput .= "
body
{
    color: white;
    font-family: Arial;
    font-size: 0.8em;
    background-color: #666666;
}

table.styledTable {
    border-top: 1px solid #ebebeb;
    border-left: 1px solid #ebebeb;
    border-right: medium none;
    margin-top: 10px;
    margin-bottom: 10px;
}

.styledTable th {
    color: #cccccc;
    background: #363636;
    border-bottom: 1px solid #ebebeb;
    padding: 6px 6px 6px 12px;
    border-top: medium none;
    border-left: medium none;
    border-right: 1px solid #ebebeb;
    text-align: left;
}

.styledTable td {
    color: #cccccc;
    border-right: 1px solid white;
    border-bottom: 1px solid white;
    padding:6px 6px 6px 12px;
}

.styledTable tr.alt {
    background-color: #898989;
}

";
$pageOutput .= "/* ]]> */\n";
$pageOutput .= "</style>\n";
$pageOutput .= "</head>\n";
$pageOutput .= "<body>\n";

$pageOutput .= "<h3>Matrix Web Services</h3>\n";
$pageOutput .= "<table summary=\"Configuration\" border=\"0\" width=\"600\" cellspacing=\"0\" cellpadding=\"5\" class=\"styledTable\">\n";
$pageOutput .= "    <tr>\n";
$pageOutput .= "        <th width=\"200\">SOAP User</th>\n";
$pageOutput .= "        <td width=\"400\">".$cfg['MatrixSOAP']['user']."</td>\n";
$pageOutput .= "    </tr>\n";
$pageOutput .= "    <tr>\n";
$pageOutput .= "        <th width=\"200\">SOAP Password</th>\n";
$pageOutput .= "        <td width=\"400\">".$cfg['MatrixSOAP']['password']."</td>\n";
$pageOutput .= "    </tr>\n";
$pageOutput .= "    <tr>\n";
$pageOutput .= "        <th width=\"200\">SOAP Server</th>\n";
$pageOutput .= "        <td width=\"400\">".$cfg['MatrixSOAP']['server']."</td>\n";
$pageOutput .= "    </tr>\n";
$pageOutput .= "</table>\n";
$pageOutput .= "<h3>Example: ".$whichFunction."</h3>\n";
$pageOutput .= "<table summary=\"".$whichFunction."\" border=\"0\" width=\"600\" cellspacing=\"0\" cellpadding=\"5\" class=\"styledTable\">\n";
$pageOutput .= "    <thead>\n";
$pageOutput .= "    <tr>\n";
$pageOutput .= "        <th width=\"250\">SOAP Key</th>\n";
$pageOutput .= "        <th width=\"350\">SOAP Value</th>\n";
$pageOutput .= "    </tr>\n";
$pageOutput .= "    </thead>\n";
$pageOutput .= "    <tbody>\n";
$pageOutput .= $pageContent;
$pageOutput .= "    </tbody>\n";
$pageOutput .= "</table>\n";
$pageOutput .= "<p>&nbsp;</p>\n";
$pageOutput .= "</body>\n";
$pageOutput .= "</html>\n";

//--------------------------------------------------------------------------->
// 26-07-2011 | Andrew DUNBAR | Output result.
//--------------------------------------------------------------------------->

print $pageOutput;
