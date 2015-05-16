<?php

$curl = \curl_init();
curl_setopt ($curl, CURLOPT_URL, 'http://www.reddit.com/r/nsfw_gif/new');
curl_setopt($curl, CURLOPT_ENCODING , "gzip");
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Cookie: over18=1"));
// curl_setopt($curl, CURLOPT_TIMEOUT, 2);

$data = curl_exec ($curl);
// print_r(curl_getinfo($curl));
curl_close ($curl);


$dom = new \DOMDocument;
$dom->loadHTML($data);

$xpath = new \DOMXPath($dom);
$query = '//a[contains(concat(" ", normalize-space(@class), " "), " title ")]';
$nodes = $xpath->query($query);

var_dump($nodes);

echo "<pre>";
for($i=0; $i<$nodes->length; $i++) {
    $node = $nodes->item($i);
    $title = $node->textContent;
    $href = $node->getAttribute('href');
    
    if(strpos($href, 'imgur') !== false) {
        echo "<img src=\"$href\" title=\"$title\"/><br/>";
    } else {
        $href = str_replace('http://', 'https://', $href);
        $vid = str_replace('www.gfycat', 'zippy.gfycat', $href) . '.webm';
        $vid2 = str_replace('www.gfycat', 'zippy.gfycat', $href) . '.mp4';
        $vid3 = str_replace('www.gfycat', 'fat.gfycat', $href) . '.mp4';
        $vid4 = str_replace('www.gfycat', 'fat.gfycat', $href) . '.webm';
        $vid5 = str_replace('www.gfycat', 'giant.gfycat', $href) . '.mp4';
        $vid6 = str_replace('www.gfycat', 'giant.gfycat', $href) . '.webm';
        ?>
        <video id="gfyVid1" class="gfyVid" width="426" height="240" autoplay="" loop="" muted="muted" poster="//thumbs.gfycat.com/CrazyElatedAmazonparrot-poster.jpg" style="display: block;">
            <source id="webmsource" src="<?= $vid; ?>" type="video/webm">
            <source id="mp4source" src="<?= $vid2; ?>" type="video/mp4">
            <source id="mp4source2" src="<?= $vid3; ?>" type="video/mp4">
            <source id="mp4source3" src="<?= $vid4; ?>" type="video/webm">
            <source id="mp4source4" src="<?= $vid5; ?>" type="video/mp4">
            <source id="mp4source5" src="<?= $vid6; ?>" type="video/webm">
            Sorry, you don't have HTML5 video and we didn't catch this properly in javascript.  
            You can try to view the gif directly: <a href="http://zippy.gfycat.com/CrazyElatedAmazonparrot.gif">http://zippy.gfycat.com/CrazyElatedAmazonparrot.gif</a>. 
        </video>
        <?php
    }
}
echo "</pre>";