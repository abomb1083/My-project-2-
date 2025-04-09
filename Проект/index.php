<?php
require 'auth.php';
require 'db.php';
include 'header.php';

// Получение мероприятий и мастер-классов
$sql = "SELECT 
            e.id AS event_id, e.name AS event_name, e.date, e.location, e.description,
            m.id AS mc_id, m.title, m.teacher, m.group_name, m.attendees_count
        FROM events e
        LEFT JOIN master_classes m ON e.id = m.event_id
        ORDER BY e.date DESC, m.id ASC";
$stmt = $pdo->query($sql);

$events = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['event_id'];
    if (!isset($events[$id])) {
        $events[$id] = [
            'id' => $id,
            'name' => $row['event_name'],
            'date' => $row['date'],
            'location' => $row['location'],
            'description' => $row['description'],
            'master_classes' => []
        ];
    }

    if ($row['mc_id']) {
        $events[$id]['master_classes'][] = [
            'id' => $row['mc_id'],
            'title' => $row['title'],
            'teacher' => $row['teacher'],
            'group_name' => $row['group_name'],
            'attendees_count' => $row['attendees_count']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Учёт мероприятий</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Учёт мероприятий</h1>
    <a href="add_full_event.php" class="btn" style="background-color: #4caf50;">+ Добавить мероприятие и мастер-классы</a>

    <?php foreach ($events as $event): ?>
        <div class="event-card" id="event-<?= $event['id'] ?>">
            <h2>
                <span class="event-name"><?= htmlspecialchars($event['name']) ?></span>
                <span style="font-weight: normal; color: #666;">| 
                    <span class="event-date"><?= htmlspecialchars($event['date']) ?></span>
                </span>
            </h2>
            <p><strong>Место:</strong> <span class="event-location"><?= htmlspecialchars($event['location']) ?></span></p>
            <p><strong>Описание:</strong> <span class="event-description"><?= htmlspecialchars($event['description']) ?></span></p>

            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 10px;">
                <button class="btn" style="background-color: #1976d2;" onclick="enableEventEdit(<?= $event['id'] ?>)">✏️ Редактировать</button>
                <button class="btn save-event-btn" style="background-color: #4caf50; display: none;" onclick="saveEvent(<?= $event['id'] ?>)">💾 Сохранить</button>

                <form action="delete_event.php" method="POST" onsubmit="return confirm('Удалить мероприятие и все его мастер-классы?');" style="display:inline;">
                    <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                    <button type="submit" class="btn" style="background-color: #d32f2f;">🗑️ Удалить</button>
                </form>
                <a href="create_order.php?event_id=<?= $event['id'] ?>" class="btn" style="background-color: #5e35b1;">📄 Сформировать приказ</a>
            </div>

            <?php if (!empty($event['master_classes'])): ?>
                <h3 style="margin-top: 10px;">Мастер-классы</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Название</th>
                            <th>Преподаватель</th>
                            <th>Группа</th>
                            <th>Участники</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($event['master_classes'] as $mc): ?>
    <tr id="mc-<?= $mc['id'] ?>">
        <td><span class="mc-title"><?= htmlspecialchars($mc['title']) ?></span></td>
        <td><span class="mc-teacher"><?= htmlspecialchars($mc['teacher']) ?></span></td>
        <td><span class="mc-group"><?= htmlspecialchars($mc['group_name']) ?></span></td>
        <td><span class="mc-attendees"><?= htmlspecialchars($mc['attendees_count']) ?></span></td>
        <td style="white-space: nowrap;">
            <button class="btn" style="background-color: #1976d2; padding: 5px 10px;" onclick="enableMCEdit(<?= $mc['id'] ?>)">✏️</button>
            <button class="btn save-mc-btn" style="background-color: #4caf50; padding: 5px 10px; display: none;" onclick="saveMC(<?= $mc['id'] ?>)">💾</button>
            <form action="delete_master_class.php" method="POST" onsubmit="return confirm('Удалить этот мастер-класс?');" style="display:inline;">
                <input type="hidden" name="mc_id" value="<?= $mc['id'] ?>">
                <button type="submit" class="btn" style="background-color: #d32f2f; padding: 5px 10px;">🗑️</button>
            </form>
        </td>
    </tr>
<?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: #777;">Нет мастер-классов</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<script>
function saveEvent(eventId) {
    const container = document.getElementById(`event-${eventId}`);
    const nameEl = container.querySelector('.event-name');
    const dateEl = container.querySelector('.event-date');
    const locationEl = container.querySelector('.event-location');
    const descriptionEl = container.querySelector('.event-description');

    const name = nameEl.innerText.trim();
    const date = dateEl.innerText.trim();
    const location = locationEl.innerText.trim();
    const description = descriptionEl.innerText.trim();

    fetch('update_event_ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: eventId, name, date, location, description })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            [nameEl, dateEl, locationEl, descriptionEl].forEach(el => {
                el.contentEditable = false;
                el.classList.remove('editing');
                el.style.borderBottom = 'none';
                el.style.padding = '0';
            });
            container.querySelector('.save-event-btn').style.display = 'none';
        } else {
            alert('Ошибка при сохранении');
        }
    })
    .catch(() => alert('Ошибка соединения'));
}

function enableEventEdit(eventId) {
    const container = document.getElementById(`event-${eventId}`);
    const nameEl = container.querySelector('.event-name');
    const dateEl = container.querySelector('.event-date');
    const locationEl = container.querySelector('.event-location');
    const descriptionEl = container.querySelector('.event-description');

    [nameEl, dateEl, locationEl, descriptionEl].forEach(el => {
        el.contentEditable = true;
        el.classList.add('editing');
        el.style.borderBottom = '1px dashed #888';
        el.style.padding = '2px';
    });

    nameEl.focus();
    container.querySelector('.save-event-btn').style.display = 'inline-block';
}

function enableMCEdit(mcId) {
    const row = document.getElementById(`mc-${mcId}`);
    const titleEl = row.querySelector('.mc-title');
    const teacherEl = row.querySelector('.mc-teacher');
    const groupEl = row.querySelector('.mc-group');
    const attendeesEl = row.querySelector('.mc-attendees');

    [titleEl, teacherEl, groupEl, attendeesEl].forEach(el => {
        el.contentEditable = true;
        el.classList.add('editing');
        el.style.borderBottom = '1px dashed #888';
        el.style.padding = '2px';
    });

    row.querySelector('.save-mc-btn').style.display = 'inline-block';
}

function saveMC(mcId) {
    const row = document.getElementById(`mc-${mcId}`);
    const titleEl = row.querySelector('.mc-title');
    const teacherEl = row.querySelector('.mc-teacher');
    const groupEl = row.querySelector('.mc-group');
    const attendeesEl = row.querySelector('.mc-attendees');

    const data = {
        id: mcId,
        title: titleEl.innerText.trim(),
        teacher: teacherEl.innerText.trim(),
        group_name: groupEl.innerText.trim(),
        attendees_count: parseInt(attendeesEl.innerText.trim())
    };

    fetch('update_master_class_ajax.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            [titleEl, teacherEl, groupEl, attendeesEl].forEach(el => {
                el.contentEditable = false;
                el.classList.remove('editing');
                el.style.borderBottom = 'none';
                el.style.padding = '0';
            });
            row.querySelector('.save-mc-btn').style.display = 'none';
        } else {
            alert('Ошибка при сохранении');
        }
    })
    .catch(() => alert('Ошибка соединения'));
}
</script>

</body>
</html>
