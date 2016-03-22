<pre>
<?php

require_once './Viewer/Bootstrap.php';


$gr = $_database->query("

select ch_name_id, ch_id
FROM `cv_chapter`
GROUP BY ch_name_id
ORDER BY `ch_id` DESC limit 0,10

");

$de = $gr->fetchAll(PDO::FETCH_ASSOC);
var_dump($de);
//foreach ($de as $be)
//echo '<pre>'.$be['ch_name_id'].'</pre>';


?>

</pre>





