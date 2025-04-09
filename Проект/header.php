<!-- header.php -->
<header>
    <div class="header-container">
        <img src="assets/logo.png" alt="Логотип" class="logo">
        <div class="buttons">
            <a href="index.php" class="btn">Главная</a>
            <a href="orders.php" class="btn">📚 Все приказы</a>
            <a href="stats.php" class="btn">📊 Статистика</a>
            <a href="logout.php" class="btn">Выйти</a>
            <button id="theme-toggle" class="btn">🌙</button>
        </div>
    </div>
</header>

<style>
    header {
        width: 100%;
        max-width: 900px;
        padding: 20px;
        margin: 0 auto;
        background-color: var(--bg-color);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo {
        height: 100px;
    }

    .buttons {
        display: flex;
        gap: 10px;
    }

    .btn {
        background-color: #4b6cb7;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        cursor: pointer;
        transition: background 0.3s ease;
        border: none;
    }

    .btn:hover {
        background-color: #3f51b5;
    }

    body.dark header {
        background-color: #1e1e2e;
    }

    body.dark .btn {
        background-color: #7f5af0;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const body = document.body;
        const theme = localStorage.getItem('theme') || 'light';
        if (theme === 'dark') {
            body.classList.add('dark');
            document.documentElement.classList.add('dark');
            document.getElementById('theme-toggle').textContent = '☀️';
        } else {
            document.getElementById('theme-toggle').textContent = '🌙';
        }
    });

    document.addEventListener('click', function (e) {
        if (e.target.id === 'theme-toggle') {
            const body = document.body;
            const isDark = body.classList.toggle('dark');
            document.documentElement.classList.toggle('dark', isDark);
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            document.cookie = `theme=${isDark ? 'dark' : 'light'}; path=/;`;
            e.target.textContent = isDark ? '☀️' : '🌙';
        }
    });
</script>
