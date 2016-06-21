<pre>
<?php

require_once './Viewer/Bootstrap.php';

$g = array('','');
$input = filter_input_array(INPUT_POST);
var_dump($input );
trim_array($input);
/*
$gr = $_database->query("

select * 
FROM `cv_chapter`
group by `ch_name_id`
ORDER BY ch_id DESC limit 0,10

");

$de = $gr->fetchAll(PDO::FETCH_ASSOC);
var_dump($de);
//foreach ($de as $be)
//echo '<pre>'.$be['ch_name_id'].'</pre>';

*/
?>

</pre>





