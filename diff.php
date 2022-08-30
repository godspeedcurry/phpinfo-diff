<?php 
ini_set('display_errors', 'Off');

function curl_website($url, $username = NULL, $password = NULL)
{
    if (preg_match('/^https?:\/\//i', $url)) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        if (!is_null($username) && !is_null($password)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode($username . ':' . $password)));
        }

        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    } else {
        return FALSE;
    }
}

function phpinfo_to_array($data)
{
    $ret = $match = array();
    if (preg_match_all('/<tr><td class="e">(.*?)<\/td><td class="v">([\s\S]*?)<\/td>(:?<td class="v">(.*?)<\/td>)?<\/tr>/', $data, $match, PREG_SET_ORDER)) {
        foreach ($match as $key => $val) {
            $ret[$val[1]] = $val[2];
        }
    }
    return $ret;
}

// compare two different phpinfos
$target_site = 'http://localhost:10021/1.html';
$example_site = 'http://150.158.58.29/index.php';

// if required, use curl_website($target_site,username,password);
$data1 = curl_website($target_site);
$data2 = curl_website($example_site);

$array1 = phpinfo_to_array($data1);
$array2 = phpinfo_to_array($data2);
// print_r($array1);
echo '<pre>';

echo '<h1>Compare Two phpinfo() Files</h1>';

echo '<p>Comparison between <span style="color: red;"> target: ' . $target_site . '</span> and <span style="color: blue;"> example: ' . $example_site . '</span></p>';

foreach($array1 as $key=>$val){
    if(array_key_exists($key,$array2)){
        $val1 = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/", " ", ($array1[$key]));
        $val2 = preg_replace("/(\s|\&nbsp\;|　|\xc2\xa0)/", " ", ($array2[$key]));
        $val1 = str_replace(", ","<br>\t----&nbsp;",$val1);
        $val2 = str_replace(", ","<br>\t++++&nbsp;",$val2);
        if(trim($val2) !== trim($val1)){
            echo("<p> <span style='color: black;'>[*]".$key."</span></p>");
            echo("<p> <span style='color: red;'>\t---- ".$val1."</span></p>");
            echo("<p> <span style='color: blue;'>\t++++ ".$val2."</span></p>");
        }
    }
    else{
        if($_GET['all']==1){
            echo("<p> <span style='color: blueviolet;'>[*]".$key."</span></p>");
            echo("<p> <span style='color: blueviolet;'>\t---- ".$array1[$key]."</span></p>");
        }
        
    }
}

