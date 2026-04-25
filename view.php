<?php
$code = preg_replace('/[^a-zA-Z0-9]/', '', $_GET['file']);
$file = "uploads/" . $code . ".html";

if(file_exists($file)){
    echo file_get_contents($file);
} else {
    echo "❌ File Not Found!";
}