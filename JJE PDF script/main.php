<?php

require 'vendor/autoload.php';

// disable DOMPDF's internal autoloader if you are using Composer
define('DOMPDF_ENABLE_AUTOLOAD', false);

// include DOMPDF's default configuration
require_once 'vendor/dompdf/dompdf/dompdf_config.inc.php';


$connect = mysqli_connect('142.51.1.106','ctsUser','!ramrodcigar!');
mysqli_select_db($connect, 'intranet_jje');
mysqli_set_charset($connect, "utf8");

set_time_limit(10000);
ini_set('memory_limit', '1000M');

$queryProg = "Select user_id from tblstatus where owners ='999'";
$usernames = mysqli_query($connect, $queryProg) or die(mysqli_error($connect));

/*$zipname = 'jje-pdfs.zip';
$zip = new ZipArchive;
$zip->open($zipname, ZipArchive::CREATE);*/

while($userRow=mysqli_fetch_assoc($usernames)){

    $us = $userRow["user_id"];

    $ques = array();
    $quesLabels = array();


    for($i=1;$i<=53;$i++){

        $queryProg = "Select text_en,questionLabel from tblquestions where ques_id ='$i'";
        $result = mysqli_query($connect, $queryProg) or die(mysqli_error($connect));

        while($row=mysqli_fetch_assoc($result)){

            $ques[$i] = $row["text_en"];
            $quesLabels[$i] = $row["questionLabel"];


        }
    }

    $htmlString = '';
    ob_start();
    echo '<html><body>';

    for($i=1;$i<=53;$i++){

        $queryProg = "Select answer from tblanswers where user_id like '$us' and ques_id='$i' order by datetimestamp desc limit 1";
        $result = mysqli_query($connect, $queryProg) or die(mysqli_error($connect));

        while($row=mysqli_fetch_assoc($result)){

            echo "<b>".$quesLabels[$i].") ".$ques[$i]."</b><br>";
            echo $row["answer"]."<br><br><hr>";

        }
    }

    echo '</body></html>';
    $htmlString = ob_get_clean();

    $dompdf = new DOMPDF();
    $dompdf->load_html($htmlString);
    $dompdf->render();
    //$dompdf->stream($us.".pdf");
    $output = $dompdf->output();
    file_put_contents($us.".pdf", $output);
    //$zip->addFile($us.".pdf");

}

/*$zip->close();
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename='.$zipname);
header('Content-Length:'.filesize($zipname));
readfile($zipname);
unlink($zipname);*/

mysqli_close($connect);

?>