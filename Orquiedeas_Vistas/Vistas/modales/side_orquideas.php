<style>
    /* Estilo para el sidebar */
.sidebar {
    width: 250px; /* Define el ancho fijo del sidebar */
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    background-color: #333; /* Color de fondo del sidebar */
    color: white;
    overflow-y: auto;
    z-index: 1000; /* Asegura que el sidebar esté encima del contenido */
    padding-top: 20px;
}

/* Estilo para el contenido principal */
#contenido-principal {
    margin-left: 250px; /* El margen izquierdo coincide con el ancho del sidebar */
    padding: 20px;
}

/* Ajuste para pantallas pequeñas */
@media (max-width: 768px) {
    .sidebar {
        width: 200px; /* Ajuste el ancho en pantallas pequeñas */
    }
    #contenido-principal {
        margin-left: 200px; /* Ajusta el margen en pantallas pequeñas */
    }
}

</style>
<div class="sidebar" id="sidebar">
    <button class="toggle-button" id="toggle-button">☰</button>
    <ul>
        <li><a href="Dashboard.php" class="no-ajax"><i class="fas fa-home"></i> <span>Inicio</span></a></li>
        <li><a href="Neva_orquidea.php" data-target="Neva_orquidea.php"><i class="fas fa-seedling"></i> <span>Registro de Orquídeas</span></a></li>
        <li><a href="#" data-target="../Vistas/Cruds/Crud_Orquideas.php"><i class="fas fa-users"></i> <span>Ver Orquídeas</span></a></li>
        <!-- Opción para abrir el PDF desde el sidebar -->
        <li>
            <a id="open-pdf-sidebar" href="#"><i class="fas fa-file-download"></i> <span>Abrir PDF</span></a>
        </li>
    </ul>
</div>

<script>
    // Evento para abrir el PDF en una nueva ventana o pestaña
    document.getElementById('open-pdf-sidebar').addEventListener('click', function(e) {
        e.preventDefault(); // Prevenir la acción predeterminada del enlace

        const pdfUrl = './modales/class.pdf'; // Ajustar según la ubicación real del archivo PDF

        // Verificar si el archivo está disponible
        fetch(pdfUrl, { method: 'HEAD' })
            .then(response => {
                if (response.ok) {
                    // Abrir el PDF en una nueva ventana o pestaña
                    window.open(pdfUrl, '_blank');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'El archivo no está disponible.',
                    });
                    console.error('Error: Archivo no encontrado');
                }
            })
            .catch(error => console.error('Error en la solicitud:', error));
    });

    // Código para manejar la navegación en el sidebar
    $('ul li a').on('click', function(e) {
        const link = $(this).attr('href');
        if (!link) {
            console.error('No se encontró un enlace válido');
            return;
        }

        if (isFormDirty && !isFormSubmitted) {
            e.preventDefault();
            Swal.fire({
                title: 'Tienes cambios sin guardar',
                text: '¿Estás seguro de que quieres salir sin guardar?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, salir',
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    isFormDirty = false;
                    window.location.assign(link);
                }
            });
        } else {
            e.preventDefault();
            window.location.assign(link);
        }
    });
</script>
