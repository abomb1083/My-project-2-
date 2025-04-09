<?php
require 'auth.php';
require 'db.php';

include 'header.php'; 

$mode = $_POST['mode'] ?? null;
$start = $_POST['start_date'] ?? null;
$end = $_POST['end_date'] ?? null;

$results = [];

function fetchStats($pdo, $query, $params = []) {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($mode === 'monthly_events') {
    $results = fetchStats($pdo, "
        SELECT to_char(date, 'YYYY-MM') AS month, COUNT(*) AS event_count, STRING_AGG(name, ', ') AS event_names
        FROM events
        WHERE date >= date_trunc('month', CURRENT_DATE)
        GROUP BY 1
        ORDER BY 1 DESC
    ");
} elseif ($mode === 'monthly_teachers') {
    $results = fetchStats($pdo, "
        SELECT mc.teacher, SUM(mc.attendees_count) AS total
        FROM master_classes mc
        JOIN events e ON mc.event_id = e.id
        WHERE e.date >= date_trunc('month', CURRENT_DATE)
        GROUP BY mc.teacher
        ORDER BY total DESC
    ");
} elseif ($mode === 'custom_range' && $start && $end) {
    $results['events'] = fetchStats($pdo, "
        SELECT to_char(date, 'YYYY-MM-DD') AS day, COUNT(*) AS event_count,
               STRING_AGG(name, ', ') AS event_names
        FROM events
        WHERE date BETWEEN ? AND ?
        GROUP BY 1
        ORDER BY 1
    ", [$start, $end]);

    $results['teachers'] = fetchStats($pdo, "
        SELECT mc.teacher, SUM(mc.attendees_count) AS total
        FROM master_classes mc
        JOIN events e ON mc.event_id = e.id
        WHERE e.date BETWEEN ? AND ?
        GROUP BY mc.teacher
        ORDER BY total DESC
    ", [$start, $end]);
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Статистика мероприятий</title>
    <link rel="stylesheet" href="style.css">
    <style>
        form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .results {
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 8px 16px;
            background-color: #1976d2;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
    </style>
</head>
<body class="container">
    <a href="index.php" class="back-btn">← Назад к списку мероприятий</a>

    <h1>📊 Статистика мероприятий</h1>

    <form method="post">
    <button type="submit" name="mode" value="monthly_events">📅 Мероприятия за месяц</button>
    <button type="submit" name="mode" value="monthly_teachers">👨‍🏫 Контингент по преподавателям</button>
    <button type="button" id="show-period">📆 Статистика за период</button>
</form>

<form method="post" id="period-form" style="display: none;">
    <input type="date" name="start_date" required>
    <input type="date" name="end_date" required>
    <input type="hidden" name="mode" value="custom_range">
    <button type="submit">Показать</button>
</form>

    <div class="results">
        <?php if ($mode === 'monthly_events' && $results): ?>
            <h2>Мероприятия за месяц</h2>
            <table>
                <tr><th>Месяц</th><th>Количество мероприятий</th><th>Названия мероприятий</th></tr>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?= $row['month'] ?></td>
                        <td><?= $row['event_count'] ?></td>
                        <td><?= htmlspecialchars($row['event_names']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php elseif ($mode === 'monthly_teachers' && $results): ?>
            <h2>Контингент преподавателей за месяц</h2>
            <table>
                <tr><th>Преподаватель</th><th>Общий контингент</th></tr>
                <?php foreach ($results as $row): ?>
                    <tr><td><?= htmlspecialchars($row['teacher']) ?></td><td><?= $row['total'] ?></td></tr>
                <?php endforeach; ?>
            </table>
        <?php elseif ($mode === 'custom_range' && $results): ?>
            <h2>Мероприятия с <?= htmlspecialchars($start) ?> по <?= htmlspecialchars($end) ?></h2>
            <table>
                <tr><th>Дата</th><th>Количество мероприятий</th><th>Названия мероприятий</th></tr>
                <?php foreach ($results['events'] as $row): ?>
                    <tr>
                        <td><?= $row['day'] ?></td>
                        <td><?= $row['event_count'] ?></td>
                        <td><?= htmlspecialchars($row['event_names']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <h2>Контингент преподавателей</h2>
            <table>
                <tr><th>Преподаватель</th><th>Общий контингент</th></tr>
                <?php foreach ($results['teachers'] as $row): ?>
                    <tr><td><?= htmlspecialchars($row['teacher']) ?></td><td><?= $row['total'] ?></td></tr>
                <?php endforeach; ?>
            </table>
        <?php elseif ($mode): ?>
            <p>Нет данных для отображения.</p>
        <?php endif; ?>
    </div>
<script>
document.getElementById('show-period').addEventListener('click', function () {
    document.getElementById('period-form').style.display = 'flex';
});
</script>
</body>
</html>