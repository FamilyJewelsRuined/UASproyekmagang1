<?php
header('Content-Type: application/json; charset=utf-8');
echo json_encode($ppa, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>