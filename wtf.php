<?php

foreach ($_POST as $key => $value) {
    error_log($key . ' has the value of ' . $value);
}
if ($_POST['id'] == "One") {    // error_log($_POST['parts']);
    echo ('{"status":"OK"}');
}
?>