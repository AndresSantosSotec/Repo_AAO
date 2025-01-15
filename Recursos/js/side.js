// sidebar.js
document.addEventListener('DOMContentLoaded', function () {
    var sidebar = document.getElementById('sidebar');
    var mainContent = document.getElementById('main-content');
    
    // Estado inicial contraído
    sidebar.classList.add('collapsed');
    mainContent.classList.add('collapsed');
});

document.getElementById('toggle-button').addEventListener('click', function () {
    var sidebar = document.getElementById('sidebar');
    var mainContent = document.getElementById('main-content');
    
    // Alterna el estado de colapso
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('collapsed');
});
