<?php
require 'auth.php';
require 'db.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить мероприятие</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-box {
            background-color: var(--card-bg);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.05);
            max-width: 900px;
            margin: 30px auto;
        }

        fieldset {
            border: none;
            padding: 0;
            margin-bottom: 30px;
        }

        legend {
            font-size: 1.3em;
            font-weight: bold;
            color: var(--accent-color);
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 15px;
        }

        .mc-entry {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-buttons {
            text-align: center;
            margin-top: 20px;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 10px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        @media (max-width: 768px) {
            .mc-entry {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="container">

<?php include 'header.php'; ?>

<h1>Добавить мероприятие с мастер-классами</h1>

<div class="form-box">
    <form action="save_full_event.php" method="POST" id="event-form">
        <fieldset>
            <legend>Информация о мероприятии</legend>
            <label>Название:
                <input type="text" name="event_name" required>
            </label>
            <label>Дата:
                <input type="date" name="event_date" required>
            </label>
            <label>Место проведения:
                <input type="text" name="event_location" required>
            </label>
            <label>Описание:
                <textarea name="event_description"></textarea>
            </label>
        </fieldset>

        <fieldset id="master-classes">
            <legend>Мастер-классы</legend>
            <div class="mc-entry">
                <input type="text" name="mc_title[]" placeholder="Название" required>
                <input type="text" name="mc_teacher[]" placeholder="Преподаватель" required>
                <input type="text" name="mc_group[]" placeholder="Группа" required>
                <input type="number" name="mc_count[]" placeholder="Контингент" min="0" required>
            </div>
        </fieldset>

        <div class="form-buttons">
            <button type="button" class="btn" onclick="addMasterClass()">+ Добавить мастер-класс</button>
            <button type="submit" class="btn">💾 Сохранить</button>
        </div>
    </form>
</div>

<script>
    function addMasterClass() {
        const container = document.getElementById('master-classes');
        const entry = document.querySelector('.mc-entry');
        const clone = entry.cloneNode(true);
        clone.querySelectorAll('input').forEach(input => input.value = '');
        container.appendChild(clone);
    }
</script>

</body>
</html>
