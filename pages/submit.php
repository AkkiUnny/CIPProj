<?php
    function uploadfile(){
                    $targetDir = "FileFolder/";
                    
                    // Combine the directory path with just the original filename
                    $destFolder = $targetDir . basename($_FILES['docFile']['name']);
                    $tempfile = $_FILES['docFile']['tmp_name'];

                    // echo $destFolder . "<br>";
                    // echo $tempfile . "<br>";
                
                    if(move_uploaded_file($tempfile, $destFolder)){
                        echo ("Your paper has been uploaded successfully!");
                    } else {
                        echo ("Trying to upload your paper again. Make sure the file is not too large and is in an accepted format.");
                    }

                    $targetDir = "FileFolder/";
                    $dirlist = scandir($targetDir, 1); 

                    // foreach($dirlist as $listing){
                    //     echo "<a href='FileFolder/" . urlencode($listing) . "'> {$listing} </a><br> ";
                    // }

                }

    if (isset($_POST['Upload'])) {
    uploadfile();
    }
?>
<div class="panel">
    <div class="panel-title">Submit Research Paper</div>
    <p>Upload yo shie</p>
    <form align="center" action="" method="post" enctype="multipart/form-data">
        <br>
        <input type="file" name="docFile">
        <br>
        <input type="submit" name="Upload" value="Upload">
    </form>
</div>
