<?php

require_once './Viewer/Bootstrap.php';

$gr = $_database->query("

select *, substring(na_name, 1, 1) as letter from cv_name order by letter

");
$names = array();
foreach ($gr->fetchAll(PDO::FETCH_ASSOC) as $name)
    $names[$name['letter']][] = $name;

foreach($names as $letter=>$name)
{
    echo '<p><h1>'.$letter.'</h1>';
    foreach($name as $detail)
        echo $detail['na_name'].'<br>';
    echo '</p>';
}
//var_dump($de);
/*
select 
   Substring(na_name, 1, 1) as letter
 , group_concat( na_name,'{,,}', na_name_uri, '{,,}', na_image_id separator '{,}') as name
from
 cv_name
group by 
 letter asc



*/
?>






