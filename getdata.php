<?php
require 'config.php';

$sql = "SELECT * FROM tbl_temperature ORDER BY id DESC LIMIT 30";
$result = $db->query($sql);

if (!$result) {
    echo "Error: " . $sql . "<br>" . $db->error;
}

$row = $result->fetch_assoc();
echo json_encode($row);
?>
