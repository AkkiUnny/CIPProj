<?php
    // uploadfile() handles the submitted research paper upload and saves metadata.
    // It reads values from $_FILES and $_POST, writes the file into FileFolder, and
    // creates a .meta file containing the form metadata.
    function uploadfile(){
        // Path to the folder where uploaded documents are stored.
        $targetDir = "FileFolder/";
                    
        // Use the original filename and combine it with the upload folder path.
        // basename() prevents directory traversal attacks in the filename.
        $destFolder = $targetDir . basename($_FILES['docFile']['name']);

        // Temporary location of the uploaded file.
        $tempfile = $_FILES['docFile']['tmp_name'];

        // Move the uploaded file from the temporary folder into FileFolder.
        if(move_uploaded_file($tempfile, $destFolder)){

            // When the file upload succeeds, create a metadata file next to it.
            $metaFile = $destFolder . ".meta";

            // Build the metadata text from the submitted form fields.
            // These values are later read by home.php's readMeta() function.
            $metadata =
                "Title=" . $_POST['TitleTXT'] . "\n" .
                "Authors=" . $_POST['AuthorsTXT'] . "\n" .
                "Department=" . $_POST['DepartmentTXT'] . "\n" .
                "Adviser=" . $_POST['AdviserTXT'] . "\n" .
                "Keywords=" . $_POST['KeywordsTXT'] . "\n" .
                "Year=" . $_POST['ReleaseTXT'] . "\n" .
                "Course=" . $_POST['CourseTXT'] . "\n" .
                "Category=" . $_POST['CategoryTXT'] . "\n";

            // Save the metadata string into a .meta file alongside the uploaded document.
            file_put_contents($metaFile, $metadata);

            echo "Your paper has been uploaded successfully!";

        } else {
            // If file upload fails, show a user-friendly message.
            echo "Trying to upload your paper again. Make sure the file is not too large and is in an accepted format.";
        }
    }

    

    
?>
<div class="panel">
    <div class="panel-title">Submit Research Paper</div>
    <p>Upload your paper with all metadata attached.</p>
    <!-- Form collects paper metadata and the file itself for upload -->
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
    // Only call uploadfile() when the submit button named Upload is present.
    // This prevents the upload logic from running when the page is first loaded.
    if(isset($_POST['Upload'])){
        uploadfile();
    }
?>