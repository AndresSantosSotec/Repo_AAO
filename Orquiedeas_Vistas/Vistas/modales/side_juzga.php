<!-- HTML para el enlace del PDF en el sidebar -->
<div class="sidebar" id="sidebar">
    <button class="toggle-button" id="toggle-button">☰</button>
    <h2>Módulo de Juzgamiento</h2>
    <ul>
        <li><a href="Dashboard.php" class="no-ajax"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
        <li><a href="#" data-target="../Vistas/Cards/cards_juzgamiento/Add_ganador.php"><i class="fas fa-award"></i> <span>Designar Ganadores</span></a></li>
        <li><a href="juzgamiento.php" data-target="juzgamiento.php"><i class="fas fa-medal"></i> <span>Listado de Ganadores</span></a></li>
        <li>
        </li>
    </ul>
</div>

<!-- JavaScript para abrir el PDF en una nueva ventana sin verificación -->
<script>
    document.getElementById('open-pdf-sidebar').addEventListener('click', function(event) {
        event.preventDefault(); // Evita la navegación por defecto

        // Ruta absoluta al archivo PDF
        const pdfUrl = 'https://aaocoban.com/Orquiedeas_Vistas/Vistas/formato.pdf';

        // Abre el PDF directamente en una nueva ventana
        window.open(pdfUrl, '_blank');
    });
</script>
