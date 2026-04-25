<?php

$dir = "uploads/";
if(!is_dir($dir)) mkdir($dir);

$msg = "";
$newLink = "";

// ================= UPLOAD =================
if(isset($_POST['upload'])){
    $file = $_FILES['file'];

    if($file['type'] == "text/html"){

        $code = substr(md5(time()),0,6);
        $path = $dir.$code.".html";

        move_uploaded_file($file['tmp_name'],$path);

        $newLink = "https://".$_SERVER['HTTP_HOST']."/".$code;
        $msg = "✅ Uploaded Successfully!";
    } else {
        $msg = "❌ Only HTML file allowed!";
    }
}

// ================= SINGLE UPDATE =================
if(isset($_POST['update'])){
    $code = preg_replace('/[^a-zA-Z0-9]/','',$_POST['code']);
    $file = $_FILES['file'];
    $path = $dir.$code.".html";

    if(file_exists($path)){
        move_uploaded_file($file['tmp_name'],$path);
        $msg = "✅ Updated: ".$code;
    }
}

// ================= BULK UPDATE =================
if(isset($_POST['bulk_update'])){

    $codes = $_POST['codes'] ?? [];
    $file = $_FILES['bulk_file'];

    if(empty($codes)){
        $msg = "❌ Nothing selected!";
    } else {

        $content = file_get_contents($file['tmp_name']);

        foreach($codes as $code){
            $code = preg_replace('/[^a-zA-Z0-9]/','',$code);
            $path = $dir.$code.".html";

            if(file_exists($path)){
                file_put_contents($path, $content);
            }
        }

        $msg = "✅ Bulk Update Done!";
    }
}

// ================= DELETE =================
if(isset($_POST['delete'])){
    $code = preg_replace('/[^a-zA-Z0-9]/','',$_POST['code']);
    $path = $dir.$code.".html";

    if(file_exists($path)){
        unlink($path);
        $msg = "🗑 Deleted: ".$code;
    }
}

// ================= FILE LIST =================
$files = glob($dir."*.html");
$count = count($files);

?>

<!DOCTYPE html>
<html>
<head>
<title>HTML Manager</title>

<style>
body{font-family:Arial;background:#111;color:#fff;text-align:center}
.box{background:#222;padding:20px;border-radius:10px;margin:20px auto;width:95%;max-width:900px}
input,button{padding:10px;margin:5px;border:none;border-radius:5px}
button{background:#00ff99;cursor:pointer}
table{width:100%;border-collapse:collapse;margin-top:20px}
td{padding:10px;border-bottom:1px solid #444}
a{color:#00ff99;text-decoration:none}
#toast{
position:fixed;
bottom:20px;
left:50%;
transform:translateX(-50%);
background:#00ff99;
color:#000;
padding:10px;
border-radius:5px;
display:none;
}
</style>

</head>
<body>

<div class="box">
<h2>Upload HTML</h2>

<form method="POST" enctype="multipart/form-data">
<input type="file" name="file" required>
<button name="upload">Upload</button>
</form>

<p><?php echo $msg; ?></p>
<p>Total Files: <?php echo $count; ?></p>

<?php if($newLink != ""): ?>
<input type="text" value="<?php echo $newLink; ?>" id="linkBox" readonly>
<button onclick="copyLink()">Copy</button>
<?php endif; ?>

</div>

<div class="box">
<h2>All Files</h2>

<form method="POST" enctype="multipart/form-data">

<table>
<tr>
<td>Select</td>
<td>Code</td>
<td>Link</td>
<td>Actions</td>
</tr>

<?php foreach($files as $f): 
$code = basename($f,".html");
$link = "https://".$_SERVER['HTTP_HOST']."/".$code;
?>

<tr>

<td>
<input type="checkbox" name="codes[]" value="<?php echo $code; ?>">
</td>

<td><?php echo $code; ?></td>

<td>
<input type="text" value="<?php echo $link; ?>" readonly onclick="copyThis(this)">
</td>

<td>

<!-- UPDATE -->
<form method="POST" enctype="multipart/form-data" style="display:inline;">
<input type="hidden" name="code" value="<?php echo $code; ?>">
<input type="file" name="file" required>
<button name="update">Update</button>
</form>

<!-- DELETE -->
<form method="POST" style="display:inline;" onsubmit="return confirm('Delete this file?')">
<input type="hidden" name="code" value="<?php echo $code; ?>">
<button name="delete" style="background:red;color:#fff;">Delete</button>
</form>

</td>

</tr>

<?php endforeach; ?>

</table>

<br>

<h3>Bulk Update</h3>
<input type="file" name="bulk_file" required>
<button name="bulk_update">Update Selected</button>

</form>

</div>

<div id="toast">Copied!</div>

<script>
function copyLink(){
    var copyText = document.getElementById("linkBox");
    copyText.select();
    document.execCommand("copy");

    showToast("✅ Link Copied!");
}

function copyThis(el){
    el.select();
    document.execCommand("copy");

    showToast("✅ Link Copied!");
}

function showToast(msg){
    var toast = document.getElementById("toast");
    toast.innerText = msg;
    toast.style.display = "block";
    setTimeout(()=>{toast.style.display="none";},1000);
}
</script>

</body>
</html>