#!/usr/bin/php
<?php
// Peter Logan 7.2.2017
// should be run at 6ish every morning to analyse the latest RASP spot blipmaps
require_once('twitter-api-php-master/TwitterAPIExchange.php');

$i = 0;
$tweetstring = ""; 
while ($i <= 6) {
    echo $i;
    $ch = curl_init();
    // Seems to work.... http://rasp-uk.uk/perl/get_rasp_blipspot.cgi?lat=54.266678&lon=-2.200172&region=UK%2b5&day=0&grid=d2&linfo=1&param=starshg&time=1300lst
    curl_setopt($ch, CURLOPT_URL,"http://rasp-uk.uk/perl/get_rasp_blipspot.cgi");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "region=UK%2b" . $i . "&grid=d2&day=0&linfo=1&lat=54.272549&lon=-2.200699&time=1300lst&param=starshg");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    // receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec ($ch);
    curl_close ($ch);
    //print_r ($server_output);
    $prepday = preg_split ("/:/", $server_output);
    //print_r ($prepday);
    $thisday = substr ($prepday[1], 2, 3);
    $textstar = preg_split ("/--------------------/", $server_output); 
    //$textstar = "0.04";
    $starpart = preg_split("/FL/", $textstar[2]);
    //$starpart = str_replace(' ', '', $starpart);
    var_dump($starpart[1]);
    $bigstar = floatval($starpart[1]);
    //var_dump($bigstar);
    $smallstar = round ($bigstar, 1);
    //var_dump($smallstar);
    $thisstar = strval ($smallstar);
    $entry = $thisday . "-" . $thisstar . "*". "   ";
    $tweetstring = $tweetstring . $entry;
    $i++;
}
echo $tweetstring;

// Tweet the result.
$combine = '#DalesRASP  '. $tweetstring;
$tweet = substr($combine, 0, 139);
$status = stripslashes(urldecode($tweet));

// add some hashtags if there's space
$statuslen = strlen($status);
if($statuslen < 86){
	$statustagged = $status." #Paragliding #YorkshireDales #forecast #RASP";
}	elseif ($statuslen < 97){
	$statustagged = $status." #Paragliding #YorkshireDales #forecast";
}	elseif ($statuslen < 111){
	$statustagged = $status." #Paragliding #YorkshireDales";
}	elseif ($statuslen < 127){
	$statustagged = $status." #Paragliding";
}
$twitsettings = array(
    'oauth_access_token' => "180106426-5GPTViE77sIG7hJUMmEZwzKrBecS7F4toGHcUG2F",
    'oauth_access_token_secret' => "W98FgDwo3hlOpfYVbGuFEXF9pjHeAQtTBVMUDIVFD8",
    'consumer_key' => "4Eoaohe138gFEaJvCzMNA",
    'consumer_secret' => "Fv8IjN5iOzOpJ4KQX6qWzXiAOZ0Bp5LPYgDw2weLI"
);
date_default_timezone_set('Europe/London');
$url = 'https://api.twitter.com/1.1/statuses/update.json';
$requestMethod = 'POST';
$postfields = array('status' => $statustagged);

$twitter = new TwitterAPIExchange($twitsettings);
echo $twitter->buildOauth($url, $requestMethod)
    ->setPostfields($postfields)
    ->performRequest();
?>
