<?php
include("config/db.php");

// Validate Programme ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo "<p>Invalid programme ID.</p>";
    exit;
}
$programmeId = (int)$_GET['id'];

// Fetch programme details securely, with optional extra columns if present
$requiredColumns = ['ProgrammeName', 'Description'];
$optionalColumns = ['Duration', 'EntryRequirements', 'LearningOutcomes', 'CareerPaths', 'ImageUrl'];

$columnQuery = $conn->query("SHOW COLUMNS FROM Programmes");
$columns = [];
while ($col = $columnQuery->fetch_assoc()) {
    $columns[] = $col['Field'];
}

$selectColumns = $requiredColumns;
foreach ($optionalColumns as $opt) {
    if (in_array($opt, $columns)) {
        $selectColumns[] = $opt;
    }
}

$selectList = implode(', ', $selectColumns);
$stmt = $conn->prepare("SELECT $selectList FROM Programmes WHERE ProgrammeID = ?");
$stmt->bind_param("i", $programmeId);
$stmt->execute();
$programmeResult = $stmt->get_result();
$programme = $programmeResult->fetch_assoc();

if (!$programme) {
    http_response_code(404);
    echo "<p>Programme not found.</p>";
    exit;
}

// Programme image fallback (update table with ImageUrl column if available)
$programmeImage = isset($programme['ImageUrl']) && !empty($programme['ImageUrl'])
    ? $programme['ImageUrl']
    : 'images/programme-placeholder.jpg';

$programmeDescription = !empty($programme['Description'])
    ? $programme['Description']
    : 'No detailed description is available for this programme yet. Please check back later for more information.';

$programmeDuration = !empty($programme['Duration']) ? $programme['Duration'] : 'N/A';
$programmeEntryRequirements = !empty($programme['EntryRequirements']) ? $programme['EntryRequirements'] : 'General entry requirements include a completed secondary education, a personal statement, and two references. Contact admissions for program-specific requirements.';
$programmeLearningOutcomes = !empty($programme['LearningOutcomes']) ? $programme['LearningOutcomes'] : 'Graduates will be able to apply theory to practice, solve complex problems, and demonstrate professional skills in the workplace.';
$programmeCareerPaths = !empty($programme['CareerPaths']) ? $programme['CareerPaths'] : 'Suitable career paths include management, specialist practitioner, consultancy, and further academic research.';

// Fetch modules for the programme
$stmt2 = $conn->prepare(
    "SELECT m.ModuleName, pm.Year
     FROM ProgrammeModules pm
     JOIN Modules m ON pm.ModuleID = m.ModuleID
     WHERE pm.ProgrammeID = ?
     ORDER BY pm.Year, m.ModuleName"
);
$stmt2->bind_param("i", $programmeId);
$stmt2->execute();
$modulesResult = $stmt2->get_result();

$modulesByYear = [];
while ($m = $modulesResult->fetch_assoc()) {
    $modulesByYear[$m['Year']][] = $m['ModuleName'];
}

$stmt->close();
$stmt2->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($programme['ProgrammeName']); ?> - Details</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: linear-gradient(130deg, #021124 0%, #122f49 48%, #1b5a73 100%);
            color: #eaf6ff;
            margin: 0;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 20% 20%, rgba(61, 222, 255, 0.18), transparent 28%), radial-gradient(circle at 80% 80%, rgba(163, 121, 255, 0.16), transparent 30%);
            pointer-events: none;
            z-index: 0;
            animation: glow 12s ease-in-out infinite alternate;
        }
        @keyframes glow {
            from { filter: blur(0px); }
            to { filter: blur(8px); }
        }
        .programme-details {
            max-width: 960px;
            margin: 2rem auto;
            font-family: 'Inter', Arial, sans-serif;
            background: rgba(9, 20, 38, 0.78);
            border: 1px solid rgba(135, 169, 216, 0.25);
            border-radius: 18px;
            box-shadow: 0 20px 40px rgba(5, 13, 28, 0.45);
            padding: 1.5rem;
            backdrop-filter: blur(12px);
            position: relative;
            z-index: 1;
        }
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(5, 19, 38, 0.55);
            border: 1px solid rgba(111, 193, 255, 0.25);
            border-radius: 10px;
            padding: 0.65rem 1rem;
            margin-bottom: 1rem;
            color: #b8e6ff;
        }
        .topbar strong {
            color: #fff;
        }
        .btn-group {
            display: flex;
            gap: 0.5rem;
        }
        .btn-modern {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            padding: 0.5rem 0.9rem;
            border-radius: 999px;
            font-size: 0.9rem;
            font-weight: 700;
            border: 0;
            color: #ffffff;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
        }
        .btn-modern:hover { transform: translateY(-1px); opacity: 0.95; }
        .btn-apply {
            background: linear-gradient(110deg, #31b2f7, #3f75ff);
            box-shadow: 0 8px 20px rgba(42, 133, 219, 0.36);
        }
        .btn-brochure {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(182, 221, 255, 0.5);
            color: #b7e9ff;
        }

        .programme-details a {
            display: inline-block;
            color: #60e2ff;
            text-decoration: none;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        .programme-header {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            margin-bottom: 1.4rem;
        }
        .programme-image {
            width: 220px;
            height: 150px;
            object-fit: cover;
            border: 2px solid #64c8e7;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.35);
        }
        .programme-meta h1 {
            margin: 0 0 0.35rem 0;
            font-size: 2rem;
            color: #ffffff;
            text-shadow: 0 1px 10px rgba(0, 0, 0, 0.42);
        }
        .chip-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 0.6rem 0 0.8rem;
        }
        .chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: linear-gradient(110deg, rgba(97, 158, 210, 0.42), rgba(60, 157, 196, 0.14));
            border: 1px solid rgba(161, 222, 255, 0.45);
            border-radius: 999px;
            color: #e4faff;
            padding: 0.25rem 0.8rem;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .programme-meta p {
            line-height: 1.6;
            color: #f2f8ff;
        }
        .programme-meta p strong {
            color: #e8f6ff;
        }
        .programme-modules h2 {
            margin: 1rem 0 0.5rem;
            color: #ffffe1;
        }
        .programme-modules__grid {
            display: grid;
            gap: 0.8rem;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            margin-top: 0.8rem;
        }
        .module-card {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 12px;
            padding: 0.9rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .module-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 22px rgba(13, 53, 92, 0.35);
        }
        .module-card h3 {
            margin: 0 0 0.45rem;
            font-size: 1rem;
            color: #deefff;
        }
        .module-card span {
            display: inline-block;
            margin-top: 0.35rem;
            color: #c4e5ff;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .programme-modules table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background: rgba(250, 250, 250, 0.12);
            border-radius: 8px;
            overflow: hidden;
        }
        .programme-modules th, .programme-modules td {
            padding: 11px 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            text-align: left;
            color: #edf5ff;
        }
        .programme-modules th {
            background: rgba(60, 127, 162, 0.24);
        }
        .programme-modules tr:nth-child(even) {
            background: rgba(255, 255, 255, 0.06);
        }
        .programme-modules tr:hover {
            background: rgba(108, 187, 254, 0.18);
        }
        .programme-extra {
            margin-top: 1.5rem;
            border-top: 1px solid rgba(189, 228, 255, 0.25);
            padding-top: 1rem;
        }
        .programme-extra h2 {
            margin-top: 1.2rem;
            color: #d6ebff;
            font-size: 1.3rem;
        }
        .programme-extra p,
        .programme-extra ol {
            color: #e8f7ff;
            line-height: 1.65;
        }
        .programme-extra ol li {
            margin: 0.35rem 0;
        }

        @media (max-width: 850px) {
            .programme-header {
                flex-direction: column;
                align-items: stretch;
            }
            .programme-image {
                width: 100%;
                height: 220px;
            }
            .programme-modules__grid {
                grid-template-columns: 1fr;
            }
            .programme-details {
                margin: 1rem;
                padding: 1rem;
            }
        }

        @media (max-width: 500px) {
            body {
                background: linear-gradient(135deg, #0e1f27 0%, #1f3442 50%, #233f4f 100%);
            }
            .programme-meta h1 {
                font-size: 1.6rem;
            }
            .chip {
                font-size: 0.78rem;
            }
        }
    </style>
</head>
<body>
<div class="topbar">
    <span><strong>StudentCourseHub</strong> / Programme overview</span>
    <div class="btn-group">
        <button class="btn-modern btn-brochure" onclick="window.location.href='brochure.pdf'">📄 Download Brochure</button>
        <button class="btn-modern btn-apply" onclick="window.location.href='apply.php'">🚀 Apply Now</button>
    </div>
</div>

<div class="programme-details">
    <a href="programmes.php">&larr; Back to programmes</a>

    <div class="programme-header">
        <img src="<?php echo htmlspecialchars($programmeImage); ?>" alt="<?php echo htmlspecialchars($programme['ProgrammeName']); ?> image" class="programme-image">
        <div class="programme-meta">
            <h1><?php echo htmlspecialchars($programme['ProgrammeName']); ?></h1>
            <div class="chip-group">
                <div class="chip">⏱️ Duration: <?php echo htmlspecialchars($programmeDuration); ?></div>
                <div class="chip">🎓 Years: <?php echo count($modulesByYear); ?></div>
                <div class="chip">📘 Modules: <?php echo array_sum(array_map('count', $modulesByYear)); ?></div>
            </div>
            <p><?php echo nl2br(htmlspecialchars($programmeDescription)); ?></p>
            <div class="details-grid" style="display:grid;gap:0.8rem;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));margin-top:1rem;">
                <div class="card" style="background:rgba(34,57,84,.4);padding:0.75rem;border-radius:10px;border:1px solid rgba(143, 204, 255, 0.28);">
                    <h4 style="margin:0 0 .5rem;color:#cdf2ff;">Entry requirements</h4>
                    <p style="margin:0;color:#e5f8ff;line-height:1.5;"><?php echo nl2br(htmlspecialchars($programmeEntryRequirements)); ?></p>
                </div>
                <div class="card" style="background:rgba(34,57,84,.4);padding:0.75rem;border-radius:10px;border:1px solid rgba(143, 204, 255, 0.28);">
                    <h4 style="margin:0 0 .5rem;color:#cdf2ff;">Career focus</h4>
                    <p style="margin:0;color:#e5f8ff;line-height:1.5;"><?php echo nl2br(htmlspecialchars($programmeCareerPaths)); ?></p>
                </div>
            </div>
        </div>
    </div>

    <section class="programme-modules">
        <h2>Modules by Year</h2>

        <?php if (count($modulesByYear) === 0): ?>
            <p>No modules found for this programme.<p>
        <?php else: ?>
            <div class="programme-modules__grid">
                <?php foreach ($modulesByYear as $year => $moduleNames): ?>
                    <div class="module-card">
                        <h3>Year <?php echo htmlspecialchars($year); ?></h3>
                        <ul>
                            <?php foreach ($moduleNames as $name): ?>
                                <li><?php echo htmlspecialchars($name); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <span><?php echo count($moduleNames); ?> modules</span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="programme-extra">
        <h2>What you will learn</h2>
        <p><?php echo nl2br(htmlspecialchars($programmeLearningOutcomes)); ?></p>

        <h2>Career opportunities</h2>
        <p><?php echo nl2br(htmlspecialchars($programmeCareerPaths)); ?></p>

        <h2>What to do next</h2>
        <ol>
            <li>Review <a href="apply.php" style="color:#8beaff;">application instructions</a>.</li>
            <li>Prepare your transcript, CV, and references.</li>
            <li>Contact admissions at <a href="mailto:admissions@university.example" style="color:#8beaff;">admissions@university.example</a>.</li>
            <li>Submit your application and track it in your portal.</li>
        </ol>
    </section>
</div>
</body>
</html>