<?php
    function uploadfile(){
                    $targetDir = "FileFolder/";
                    
                    // Combine the directory path with just the original filename
                    $destFolder = $targetDir . basename($_FILES['docFile']['name']);
                    $tempfile = $_FILES['docFile']['tmp_name'];

                    // echo $destFolder . "<br>";
                    // echo $tempfile . "<br>";
                
                    if(move_uploaded_file($tempfile, $destFolder)){

                        // Create metadata file
                        $metaFile = $destFolder . ".meta";

                        $metadata =
                            "Title=" . $_POST['TitleTXT'] . "\n" .
                            "Authors=" . $_POST['AuthorsTXT'] . "\n" .
                            "Department=" . $_POST['DepartmentTXT'] . "\n" .
                            "Adviser=" . $_POST['AdviserTXT'] . "\n" .
                            "Keywords=" . $_POST['KeywordsTXT'] . "\n" .
                            "Year=" . $_POST['ReleaseTXT'] . "\n" .
                            "Course=" . $_POST['CourseTXT'] . "\n" .
                            "Category=" . $_POST['CategoryTXT'] . "\n";

                        file_put_contents($metaFile, $metadata);

                        echo "Your paper has been uploaded successfully!";

                    } else {

                        echo "Trying to upload your paper again. Make sure the file is not too large and is in an accepted format.";

                    }

                    $targetDir = "FileFolder/";
                    $dirlist = scandir($targetDir, 1); 

                    // foreach($dirlist as $listing){
                    //     echo "<a href='FileFolder/" . urlencode($listing) . "'> {$listing} </a><br> ";
                    // }

                }

    

    
?>
<div class="panel">
    <div class="panel-title">Submit Research Paper</div>
    <p>Upload yo shie</p>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="text" name="TitleTXT" placeholder="Title of Research Paper">
        <br>
        <input type="text" name="AuthorsTXT" placeholder="Author(s)">
        <br>
        <input type="text" name="DepartmentTXT" placeholder="Department">
        <br>
        <input type="text" name="AdviserTXT" placeholder="Research Adviser">
        <br>
        <input type="text" name="KeywordsTXT" placeholder="Keywords (comma separated)">
        <br>
        <input type="number" name="ReleaseTXT" placeholder="Publication Year" min="1900" max="2100">
        <br>
        <select name="FormatTXT">
            <option value="">Select Format</option>
            <option value="PDF">PDF</option>
            <option value="DOCX">DOCX</option>
        </select>
        <br>
        <textarea name="AbstractTXT" placeholder="Abstract" rows="5" cols="40"></textarea>
        <br>
        <input type="text" name="CourseTXT" placeholder="Course / Program">
        <br>
        <input type="text" name="CategoryTXT" placeholder="Research Category">
        <br>
        <input type="file" name="docFile">
        <br>
        <input type="submit" name="Upload" value="Upload">
    </form>
</div>
<?php
    if(isset($_POST['Upload'])){
        uploadfile();
    }
?>