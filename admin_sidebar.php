<!-- admin_sidebar.php -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h2>Geek Admin</h2>
        <button id="toggle-btn">☰</button>
    </div>

    <ul class="menu">
        <li><a href="admin_dashboard.php">📥 New Feedback</a></li>
        <li><a href="admin_replied.php">💬 Replied Feedback</a></li>
        <li><a href="admin_unreplied.php">⏳ Read but Unreplied</a></li>
        <li><a href="logout.php">🚪 Logout</a></li>
    </ul>
</div>

<style>
/* Sidebar Styles */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 240px;
    height: 100%;
    background: #1e1e2f;
    color: white;
    padding: 20px;
    box-shadow: 2px 0 10px rgba(0,0,0,0.3);
    transition: transform 0.3s ease;
    z-index: 1000;
}

.sidebar.collapsed {
    transform: translateX(-100%);
}

.sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.sidebar-header h2 {
    font-size: 20px;
    margin: 0;
}

#toggle-btn {
    background: transparent;
    border: none;
    color: white;
    font-size: 22px;
    cursor: pointer;
    transition: color 0.3s;
}
#toggle-btn:hover {
    color: #4f46e5;
}

.menu {
    list-style: none;
    padding: 0;
    margin: 0;
}
.menu li {
    margin: 15px 0;
}
.menu li a {
    color: #fff;
    text-decoration: none;
    font-size: 16px;
    display: block;
    padding: 10px 12px;
    border-radius: 8px;
    transition: background 0.3s;
}
.menu li a:hover {
    background: #4f46e5;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
    .sidebar.show {
        transform: translateX(0);
    }
}
</style>

<script>
// JavaScript toggle functionality
document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById("sidebar");
    const toggleBtn = document.getElementById("toggle-btn");

    toggleBtn.addEventListener("click", () => {
        sidebar.classList.toggle("show");
    });
});
</script>
