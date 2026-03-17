<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Course Hub</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="css/style.css">
</head>



<body>

<header>

<div class="MainContainer">

<h1>Student <span>Course Hub</span></h1>

<input type="checkbox" id="nav-toggle" class="nav-toggle">
<label for="nav-toggle" class="nav-toggle-label">&#9776;</label>

<nav>
<ul>
<li><a href="index.php">Home</a></li>
<li><a href="#courses">Courses</a></li>
<li><a href="#contact">Contact</a></li>
<li><a href="#about">About</a></li>
<li><a href="#register">Register</a></li>
<li><a href="Admin/login.php">Admin</a></li>
</ul>
</nav>

</div>


<div class="information" id="about">

<h2>University of Computer Science</h2>

<p>
Explore your undergraduate and postgraduate programmes suitable for your
interests and career goals.
</p>

</div>


<div class="searchbar">

<form method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    <div class="search">
        <input type="text" name="q" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" placeholder="Search for courses, programmes, resources..." aria-label="Search programmes">
        <button type="submit">Search</button>
    </div>

    <div class="programmes">
        <?php $currentLevel = isset($_GET['level']) ? $_GET['level'] : ''; ?>
        <button type="submit" name="level" value=""<?php echo $currentLevel === '' ? ' class="active"' : ''; ?>>All</button>
        <button type="submit" name="level" value="Undergraduate"<?php echo $currentLevel === 'Undergraduate' ? ' class="active"' : ''; ?>>Undergraduate</button>
        <button type="submit" name="level" value="Postgraduate"<?php echo $currentLevel === 'Postgraduate' ? ' class="active"' : ''; ?>>Postgraduate</button>
    </div>
</form>

</div>

</header>


<?php
// process search and filters
include __DIR__ . '/config/db.php';
$search = isset($_GET['q']) ? $conn->real_escape_string(trim($_GET['q'])) : '';
$level  = isset($_GET['level']) ? $conn->real_escape_string($_GET['level']) : '';

$where = [];
if ($search !== '') {
    $where[] = "(ProgrammeName LIKE '%$search%' OR Description LIKE '%$search%')";
}
if ($level === 'Undergraduate' || $level === 'Postgraduate') {
    // assume ProgrammeLevel column stores these values
    $where[] = "ProgrammeLevel='$level'";
}

$sql = "SELECT ProgrammeID, ProgrammeName FROM Programmes";
if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$result = $conn->query($sql);
?>

<main>

<div class="course-grid" id="courses">

<?php if ($result && $result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="course-card">
            <a href="programme-details.php?id=<?php echo $row['ProgrammeID']; ?>">
                <?php echo htmlspecialchars($row['ProgrammeName']); ?>
            </a>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No programmes match your criteria.</p>
<?php endif; ?>

</div>

</main>

<?php
// registration handling
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $programme = (int)($_POST['programme'] ?? 0);
    if ($name && $email && $programme) {
        include __DIR__ . '/config/db.php';
        $stmt = $conn->prepare("INSERT INTO InterestedStudents (ProgrammeID,StudentName,Email) VALUES (?,?,?)");
        $stmt->bind_param('iss', $programme, $name, $email);
        if ($stmt->execute()) {
            $message = 'Interest registered successfully!';
        } else {
            $message = 'Unable to register interest.';
        }
    } else {
        $message = 'Please fill in all fields.';
    }
}

// fetch programmes list for dropdown
$programmeOptions = [];
include __DIR__ . '/config/db.php';
$res = $conn->query('SELECT ProgrammeID, ProgrammeName FROM Programmes ORDER BY ProgrammeName');
while ($r = $res->fetch_assoc()) {
    $programmeOptions[] = $r;
}
?>

<section class="register-section" style="padding:40px 20px; background:#fff;">
    <div class="container" id="register">
        <h2>Register Your Interest</h2>
        <?php if ($message): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>#register">
            <label>Name<br><input type="text" name="name" required></label><br><br>
            <label>Email<br><input type="email" name="email" required></label><br><br>
            <label>Programme<br>
                <select name="programme" required>
                    <option value="">Select a programme</option>
                    <?php foreach ($programmeOptions as $opt): ?>
                        <option value="<?php echo $opt['ProgrammeID']; ?>"><?php echo htmlspecialchars($opt['ProgrammeName']); ?></option>
                    <?php endforeach; ?>
                </select>
            </label><br><br>
            <button type="submit" name="register">Submit</button>
        </form>
    </div>
</section>

<footer class="footer" id="contact">

<div class="footer-container">

<div class="footer-section">

<h3>Student Course Hub</h3>

<p>Your gateway to explore programmes, courses and student resources.</p>

</div>


<div class="footer-section">

<h3>Quick Links</h3>

<ul>
<li><a href="index.php">Home</a></li>
<li><a href="#courses">Courses</a></li>
<li><a href="#">Programmes</a></li>
<li><a href="#register">Register</a></li>
</ul>

</div>


<div class="footer-section">

<h3>Contact</h3>

<p>College of Computer Science</p>
<p>Copenhagen, Denmark</p>
<p>Sankt Petri Passage 5</p>
<p>1165 København K</p>

</div>

</div>


<div class="footer-bottom">

<p>© 2026 Student Course Hub | All Rights Reserved</p>

</div>

</footer>

</body>
</html>