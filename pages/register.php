<?php
$registration_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_file = dirname(__FILE__) . '/../userlist.txt';

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $student_number = trim($_POST['student_number'] ?? '');
    $department = trim($_POST['department'] ?? '');

    if ($username === '' || $password === '' || $full_name === '' || $student_number === '' || $department === '') {

        $registration_error = "Please complete all fields before creating your account.";

    } else {

        if (!file_exists($user_file)) {
            file_put_contents($user_file, "");
        }

        $lines = file($user_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        $records = [];

        for ($i = 0; $i < count($lines); $i += 5) {

            $records[] = [
                "username" => $lines[$i] ?? '',
                "password" => $lines[$i + 1] ?? '',
                "full_name" => $lines[$i + 2] ?? '',
                "student_number" => $lines[$i + 3] ?? '',
                "department" => $lines[$i + 4] ?? '',
            ];
        }

        $duplicate = false;

        foreach ($records as $record) {
            if (
                $record['username'] === $username ||
                $record['student_number'] === $student_number
            ) {
                $duplicate = true;
                break;
            }
        }

        if ($duplicate) {

            $registration_error = "That username or student number already exists.";

        } else {

            $new_data =
                $username . "\n" .
                $password . "\n" .
                $full_name . "\n" .
                $student_number . "\n" .
                $department . "\n\n";

            file_put_contents($user_file, $new_data, FILE_APPEND | LOCK_EX);

            $_SESSION['success'] = "Account created successfully!";

            header("Location: register.php");
            exit();
        }
    }
}
?>

<div class="panel">
    <div class="panel-title">Researcher Registration</div>
    <p>Fill in the details below to create a student account and save it into the archive list.</p>

    <?php if ($registration_error !== ''): ?>
        <div class="alert-error">
            <?= htmlspecialchars($registration_error) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div style="
            background:#EAF5EA;
            border:1px solid #C6E3C6;
            color:#2F6B3B;
            padding:12px;
            border-radius:6px;
            margin-bottom:10px;
        ">
            <?= $_SESSION['success']; ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form method="POST" action="register.php">

        <div class="form-row">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="form-row">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <div class="form-row">
            <label>Full Name</label>
            <input type="text" name="full_name" required>
        </div>

        <div class="form-row">
            <label>Student Number</label>
            <input type="text" name="student_number" required>
        </div>

        <div class="form-row">
            <label>Department</label>
            <select name="department" required>
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
        </div>

        <button type="submit" class="btn-primary">
            Create Account
        </button>

    </form>
</div>