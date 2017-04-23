<html>
<head>
   <title>Doctor Who Ratings Averages</title>
   <style>
       table, th, td {
           border: 1px solid black;
           border-collapse: collapse;
       }
   </style>
</head>
<body>
<?php
$ratings = array();
#ini_set('display_errors', 1);

$pages = range(0,800,100);
$seasons = array(
    "season1" => range(1,42),     "season2" => range(43,81),     "season3" => range(82,126),     "season4" => range(127,169),
    "season5" => range(170,209),  "season6" => range(210,253),   "season7" => range(254,278),    "season8" => range(279,303),
    "season9" => range(304,329),  "season10" => range(330,355),  "season11" => range(356,381),   "season12" => range(382,401),
    "season13" => range(402,427), "season14" => range(428,453),  "season15" => range(454,479),   "season16" => range(480,505),
    "season17" => range(506,525), "season18" => range(526,553),  "season19" => range(554,579),   "season20" => range(580,601), 
    "special1983" => array(602),
    "season21" => range(603,626), "season22" => range(627,639),  "season23" => range(640,653),   "season24" => range(654,667),
    "season25" => range(668,681), "season26" => range(682,695),

    "movie" => range(696,696),

    "series1" => range(697,709),  "series2" => range(711,723),   "series3" => range(725,737),    "series4" => range(739,751),
    "specials2009" => range(752,756),
    "series5" => range(757,769),  "series6" => range(771,783),
    "series7" => array_merge(range(785,789),range(791,798)), # excludes xmas special "the snowmen"
    "specials2013" => range(799,800),
    "series8" => range(801,812),  "series9" => range(814,825),  "series10" => range(828,840)

    #"xmas_all" => array(710,724,738,752,755, 770,784,790,800,813,826,827),
    #"all" => range(1,827)
);
?>
Source code available on github: <a href='https://github.com/dresken/dw_ratings'>https://github.com/dresken/dw_ratings</a>
<br/>
You should not reference this page, it is just a calculator. Please use the following as a reference for the information: <a href='http://guide.doctorwhonews.net/info.php?detail=ratings'>http://guide.doctorwhonews.net/info.php?detail=ratings</a>
<h1>Original Sources</h1>
<ul>
<?php 
foreach ($pages as $page) {
    $file = "cache/dw-ratings-$page.html";
    $url = "http://guide.doctorwhonews.net/info.php?detail=ratings&start=$page&type=date&order=asc";
    // Decide whether to renew cache
    if (! file_exists($file) or filemtime($file) < strtotime("-1 day")) {
        file_put_contents("$file.tmp", fopen($url, 'r'));
        rename("$file.tmp", "$file");
    }
    $mtime = gmdate("Y-m-d H:i:s T", filemtime($file));
    print "<li><a href='$url'>Page $page</a> (retrieved $mtime)</li>";
    $DOM = new DOMDocument;
    $internalErrors = libxml_use_internal_errors(true); //Disable error reporting as HTML is not well formed
    $DOM->loadHTMLFile("$file");
    libxml_use_internal_errors($internalErrors);
    // Get the row objects of the table
    $tables = $DOM->getElementsByTagName('table');
    $trs = $tables->item(0)->getElementsByTagName('tr');
    // Extract data from the row (header is ignored)
    for ($i = 1; $i < $trs->length; $i++) {
        $tds = $trs->item($i)->getElementsByTagName('td');
        $index = (int) $tds->item(0)->nodeValue; // Epsiodes number
        $ratings[$index] = (float) rtrim($tds->item(6)->nodeValue, 'm'); //Rating converted to a number
    }
}
print "<li>Current time: ".gmdate("Y-m-d H:i:s T")."</li>";
?>
</ul>
<h1>Ratings averages</h1>
<table >
    <tr>
        <th>Season/Series</th>
        <th>Av. Rating (millions)</th>
        <th>Math</th>
        <th>Episodes</th>
        <th>Source</th>
    </tr>
<?php
// Get each list of episodes
foreach ($seasons as $season => $episodes) {
    $count = 0;
    $tally = 0;
    $math = "(";
    $listep = "";
    $sources = array();
    $incomplete = false;
    // For each episode tally etc
    foreach ($episodes as $episode) {
        if (isset($ratings[$episode]) && $ratings[$episode] > 0) {
            $count++;
            $tally += $ratings[$episode];
            $math .= $ratings[$episode]." + ";
            $listep .= $episode.", ";
            $source = intval(floor($episode/100)*100);
            $sources[$source] = true;
        } else {
            $incomplete = true;
        }
    }
    $math = rtrim($math,"+ ").") / $count"; //finalise printable equation
    $listep = rtrim($listep, ", ");
    if ($incomplete) { $listep .= ", INCOMPLETE"; }
    $average = round($tally / $count,2); // Calculate the average - rounded to 2 dp
    // Output info
    print "<tr>";
    print "<td>$season</td>";
    print "<td>$average</td>";
    print "<td>$math</td>";
    print "<td>$listep</td>";
    print "<td>";
    foreach ($sources as $key => $value) {
        print "<a href='http://guide.doctorwhonews.net/info.php?detail=ratings&start=$key&type=date&order=asc'>$key</a> ";
    }
    print "</td>";
    print "</tr>";
}
?>
</table>
</body>
</html>
