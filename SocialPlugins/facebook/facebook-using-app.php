<?PHP

/**
 * Description: Loads authenticated wall posts from Facebook using Facebook App.
 *
 * Developed in response to this:  https://developers.facebook.com/blog/post/509/
 * Inspired by : http://www.terrordesigns.com/facebook-graph-posts-data-now-requires-access_token
 *
 * URL Request format: /facebook-using-app.php?graphAccount=XXXXXX&graphPage=YYYYYYYY
 *
 *
 *
 * PHP Version 5
 *
 * @category PHP
 * @package  FacebookAuth_System
 * @author   Andrew DUNBAR <andrew@doubleorsm.com>
 * @license  GPL, http://www.gnu.org/licenses/gpl.htm
 * @link     http://www.doubleorsm.com
 *
*/

//--------------------------------------------------------------------------->
// 29-06-2011 | Andrew DUNBAR | Establish path.
//--------------------------------------------------------------------------->

$path = dirname(__FILE__)."/";

//--------------------------------------------------------------------------->
// 29-06-2011 | Andrew DUNBAR | Establish Facebook App details.
//--------------------------------------------------------------------------->

// YOUR-APP-ID-FROM-FACEBOOK
$appID     = "";

// YOUR-APP-SECRET-FROM-FACEBOOK
$appSecret = "";

// A LIST OF PERMITTED ACCOUNTS TO STOP THE PROXY SCRIPT FROM LOADING ANYONES CONTENT
$permittedAccounts = array("graphAccount");

//--------------------------------------------------------------------------->
// 29-06-2011 | Andrew DUNBAR | Establish vars for proxy authentication?
//--------------------------------------------------------------------------->

$cfg['proxy']['use']      = "YES";
$cfg['proxy']['host']     = "";
$cfg['proxy']['user']     = "";
$cfg['proxy']['password'] = "";
$cfg['proxy']['string']   = "".$cfg['proxy']['user'].":".$cfg['proxy']['password']."";

//--------------------------------------------------------------------------->
// 29-06-2011 | Andrew DUNBAR | Establish required incoming vars.
//--------------------------------------------------------------------------->

if (isset($_GET['graphAccount'])) {
    $graphAccount = $_GET['graphAccount'];
} else {
    $graphAccount = "";
}

if (isset($_GET['graphPage'])) {
    $graphPage = $_GET['graphPage'];
} else {
    $graphPage = "";
}

//--------------------------------------------------------------------------->
// 29-06-2011 | Andrew DUNBAR | Establish optional incoming vars.
//--------------------------------------------------------------------------->

$additionalVars = "";

foreach ($_GET as $key => $value) {

    //--------------------------------------------------------------------------->
    // 29-06-2011 | Andrew DUNBAR | Ignore two "get" vars from above.
    //--------------------------------------------------------------------------->

    if (($key != "graphAccount") and ($key != "graphPage")) {

        $additionalVars .= "&".$key."=".$value."";

    }

}

//--------------------------------------------------------------------------->
// 29-06-2011 | Andrew DUNBAR | Proceed to load content?
//--------------------------------------------------------------------------->

if (in_array($graphAccount, $permittedAccounts)) {

    //--------------------------------------------------------------------------->
    // 29-06-2011 | Andrew DUNBAR | Authenticate to Facebook for token.
    //--------------------------------------------------------------------------->

    $appToken = getTheData("https://graph.facebook.com/oauth/access_token?type=client_cred&client_id=".$appID."&client_secret=".$appSecret);

    //--------------------------------------------------------------------------->
    // 29-06-2011 | Andrew DUNBAR | Was authentication succesful?
    //--------------------------------------------------------------------------->

    if ($appToken != "-1") {

        //--------------------------------------------------------------------------->
        // 29-06-2011 | Andrew DUNBAR | Load JSON content from Facebook.
        //--------------------------------------------------------------------------->

        $appContent = getTheData("https://graph.facebook.com/".$graphAccount."/".$graphPage."?".$appToken."".$additionalVars);

        //--------------------------------------------------------------------------->
        // 29-06-2011 | Andrew DUNBAR | Was content load succesful?
        //--------------------------------------------------------------------------->

        if ($appContent != "-1") {

            //--------------------------------------------------------------------------->
            // 29-06-2011 | Andrew DUNBAR | Set return header.
            //--------------------------------------------------------------------------->

            header('Content-type: application/json');

            //--------------------------------------------------------------------------->
            // 29-06-2011 | Andrew DUNBAR | Output result to page.
            //--------------------------------------------------------------------------->

            print $appContent;

        }

    }

} else {

    print "-1";

}

//---------------------------------------------------------------------------------------------------------------------------->

/**
 * Description: Curl function for loading web content through the proxy server.
 *
 * @param string $theUrl Takes incoming 'theUrl' for specifying what web resource to load the content from.
 *
 * @return string
 *
*/

function getTheData($theUrl)
{

    //--------------------------------------------------------------------------->
    // 29-06-2011 | Andrew DUNBAR | Establish global vars.
    //--------------------------------------------------------------------------->

    global $cfg;

    //--------------------------------------------------------------------------->
    // 29-06-2011 | Andrew DUNBAR | Load remote using using curl.
    //--------------------------------------------------------------------------->

    $ch = curl_init();

    if ($ch) {

        //--------------------------------------------------------------------------->
        // 29-06-2011 | Andrew DUNBAR | Set curl options.
        //--------------------------------------------------------------------------->

         curl_setopt($ch, CURLOPT_URL, $theUrl);
         curl_setopt($ch, CURLOPT_HEADER, 0);
         curl_setopt($ch, CURLOPT_TIMEOUT, 30);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)");
         curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 0);

        //--------------------------------------------------------------------------->
        // 29-06-2011 | Andrew DUNBAR | Do we need to use a proxy account?
        //--------------------------------------------------------------------------->

        if ($cfg['proxy']['use'] == "YES") {

            curl_setopt($ch, CURLOPT_PROXY, $cfg['proxy']['host']);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $cfg['proxy']['string']);

        }

        //--------------------------------------------------------------------------->
        // 29-06-2011 | Andrew DUNBAR | Execute curl request.
        //--------------------------------------------------------------------------->

        $chresult = curl_exec($ch);

        //--------------------------------------------------------------------------->
        // 29-06-2011 | Andrew DUNBAR | Close curl request.
        //--------------------------------------------------------------------------->

        curl_close($ch);

        //--------------------------------------------------------------------------->
        // 29-06-2011 | Andrew DUNBAR | Return content.
        //--------------------------------------------------------------------------->

        return $chresult;

    } else {

        //--------------------------------------------------------------------------->
        // 29-06-2011 | Andrew DUNBAR | Return error.
        //--------------------------------------------------------------------------->

        return "-1";

    }

}

//---------------------------------------------------------------------------------------------------------------------------->
