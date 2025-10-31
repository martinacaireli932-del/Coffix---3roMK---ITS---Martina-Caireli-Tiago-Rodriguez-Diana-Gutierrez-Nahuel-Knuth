function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const body = document.body;
    
    if (!sidebar) return;
    
    if (sidebar.style.width === '250px') {
        sidebar.style.width = '0';
        body.classList.remove('sidebar-abierto');
    } else {
        sidebar.style.width = '250px';
        body.classList.add('sidebar-abierto');
    }
}

document.addEventListener('DOMContentLoaded', () => {
});