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
    <p>Upload your paper with all metadata attached.</p>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="submit-grid">
            <label>
                Title of Research Paper
                <input class="form-input" type="text" name="TitleTXT" placeholder="Enter title" required>
            </label>
            <label>
                Author(s)
                <input class="form-input" type="text" name="AuthorsTXT" placeholder="Enter authors" required>
            </label>
            <label>
                Department
                <select class="form-input" name="DepartmentTXT" required>
                    <option value="">Select a department</option>
                    <option value="CBA">CBA – College of Business Administration</option>
                    <option value="CENG">CENG – College of Engineering</option>
                    <option value="CCSS">CCSS – College of Computer Studies and Systems</option>
                    <option value="CAS">CAS – College of Arts and Sciences</option>
                    <option value="CFAD">CFAD – College of Fine Arts, Architecture and Design</option>
                    <option value="LAW">LAW – College of Law</option>
                    <option value="DENT">DENT – College of Dentistry</option>
                    <option value="GRAD">GRAD – Graduate School</option>
                </select>
            </label>
            <label>
                Research Adviser
                <input class="form-input" type="text" name="AdviserTXT" placeholder="Enter adviser" required>
            </label>
            <label>
                Keywords
                <input class="form-input" type="text" name="KeywordsTXT" placeholder="e.g. artificial intelligence, robotics">
            </label>
            <label>
                Publication Year
                <input class="form-input" type="number" name="ReleaseTXT" placeholder="2024" min="1900" max="2100">
            </label>
            <label>
                File format
                <select class="form-input" name="FormatTXT">
                    <option value="">Select format</option>
                    <option value="PDF">PDF</option>
                    <option value="DOCX">DOCX</option>
                </select>
            </label>
            <label>
                Course / Program
                <input class="form-input" type="text" name="CourseTXT" placeholder="Enter course or program">
            </label>
            <label>
                Research Category
                <select class="form-input" name="CategoryTXT">
                    <option value="">Select category</option>
                    <option value="Case study">Case study</option>
                    <option value="Survey">Survey</option>
                    <option value="Literature review">Literature review</option>
                    <option value="Experimental">Experimental</option>
                    <option value="Theoretical">Theoretical</option>
                    <option value="Design project">Design project</option>
                </select>
            </label>
            <label class="full-width">
                Upload document
                <input class="form-input" type="file" name="docFile" required>
            </label>
        </div>

        <div class="submit-actions">
            <input class="submit-button" type="submit" name="Upload" value="Upload">
        </div>
    </form>
    <style>
        .submit-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-top: 18px;
        }

        .submit-grid label {
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 12px;
            color: var(--text-muted);
        }

        .submit-grid .full-width {
            grid-column: 1 / -1;
        }

        .form-input {
            width: 100%;
            min-height: 40px;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--surface);
            color: var(--text);
            font-family: 'DM Mono', monospace;
            font-size: 13px;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .form-input:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(16, 112, 168, 0.08);
        }

        textarea.form-input {
            min-height: 120px;
            resize: vertical;
        }

        .submit-actions {
            margin-top: 22px;
            display: flex;
            justify-content: flex-end;
        }

        .submit-button {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            background: var(--accent);
            color: #F5F2EC;
            font-family: 'DM Mono', monospace;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            cursor: pointer;
            transition: background 0.15s;
        }

        .submit-button:hover {
            background: var(--accent-light);
        }
    </style>
</div>
<?php
    if(isset($_POST['Upload'])){
        uploadfile();
    }
?>