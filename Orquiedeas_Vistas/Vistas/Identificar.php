    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Reconocimiento de Orquídeas</title>

        <!-- Enlaces a Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@4.5.2/dist/minty/bootstrap.min.css">

        <!-- Enlace a FontAwesome para los íconos -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

        <!-- Estilos personalizados -->
        <link rel="stylesheet" href="../../Recursos/css/recon.css">
    </head>

    <body>

        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <ul>
                <li><a href="Dashboard.php"><i class="fas fa-home"></i> <span>Volver al Inicio</span></a></li>
            </ul>
        </div>
    <style>
        .responsive-img {
            max-width: 100%; /* La imagen no excederá el ancho de su contenedor */
            height: auto;    /* Mantiene la proporción de la imagen */
        }
        .logo-container {
            position: absolute; /* Mantiene la posición en la esquina superior derecha */
            top: 10px;
            right: 10px;
            z-index: 1000; /* Asegura que esté por encima de otros elementos */
            display: flex; /* Utiliza Flexbox para alinear los logos */
            flex-direction: row; /* Alineación horizontal */
            align-items: center; /* Centra verticalmente los logos */
        }
         }
        .main-content {
            margin-top: 80px; /* Añade margen superior para evitar superposición con los logos */
            padding: 20px; /* Espaciado interno para el contenido */
        }
    </style>
    <!-- Logo de la universidad en la esquina superior derecha -->
    <div class="logo-container">
        <!-- Segundo logo -->
        <img src="/Recursos/img/Logo-fotor-bg-remover-2024090519443.png" alt="Logo 2" class="responsive-img" style="width: 150px; margin-right: 10px;">
        <img src="/Recursos/img/hdumg.png" alt="Logo Universidad" class="responsive-img" style="width: 250px;">
        <!-- -->
    </div>

        <!-- Contenido principal -->
        <div class="main-content">
            <h1 class="text-center mt-5">Reconocimiento de Orquídeas</h1>
            <p class="text-center">Elige entre usar la cámara o cargar una imagen para identificar las orquídeas.</p>

            <!-- Botones para elegir entre usar cámara o subir imagen -->
            <div class="text-center mb-4">
                <button class="btn btn-primary" id="useCameraButton"><i class="fas fa-camera"></i> Usar Cámara</button>
                <button class="btn btn-secondary" id="uploadImageButton"><i class="fas fa-upload"></i> Cargar Imagen</button>
            </div>

            <!-- Contenedor de la cámara -->
            <div class="camera-container" id="cameraSection" style="display: none;">
                <video id="cameraStream" autoplay></video>
                <canvas id="cameraCanvas" style="display: none;"></canvas> <!-- Canvas para capturar la foto -->

                <div class="camera-buttons">
                    <button class="btn btn-success" id="openCameraButton"><i class="fas fa-camera"></i> Abrir Cámara</button>
                    <button class="btn btn-danger" id="closeCameraButton" disabled><i class="fas fa-times-circle"></i> Cerrar Cámara</button>
                    <button class="btn btn-primary" id="capturePhotoButton" disabled><i class="fas fa-camera-retro"></i> Tomar Foto</button>
                    <button class="btn btn-warning" id="recognizeButtonCamera" disabled><i class="fas fa-search"></i> Reconocer Orquídea</button>
                </div>
                <div class="camera-info" id="cameraStatus"></div>
            </div>


            <!-- Contenedor para cargar imagen -->
            <div class="upload-container" id="uploadSection" style="display: none;">
                <input type="file" id="uploadInput" accept="image/png, image/jpeg, image/jpg, image/gif" class="form-control mb-3">
                <img id="uploadedImage" src="#" alt="Imagen seleccionada" style="display: none;">
                <div class="upload-buttons">
                    <button class="btn btn-warning" id="recognizeButtonImage" style="display:none;"><i class="fas fa-search"></i> Reconocer Orquídea</button>
                </div>
                <div class="upload-info" id="uploadStatus"></div>
            </div>
        </div>

        <!-- Enlaces a Bootstrap JS y jQuery -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>

        <script>
            const apiKey = 'nPUlaG5suERaZrx4SFtzAIFmbPOQLYleqrixK9FM2eI75t0PLN'; // Reemplaza con tu API Key de Plant.id
            const apiUrl = '../Backend/recognize.php'; // Archivo PHP que manejará la solicitud


            function copiarAlPortapapeles(texto) {
                const elementoTemporal = document.createElement('textarea');
                elementoTemporal.value = texto;
                document.body.appendChild(elementoTemporal);
                elementoTemporal.select();
                document.execCommand('copy');
                document.body.removeChild(elementoTemporal);
                //alert('Nombre de la planta copiado: ' + texto);
            }


            // Función para enviar la imagen a recognize.php
            async function recognizeOrchid(base64Image) {
                const formData = new FormData();
                formData.append('image', base64Image); // Enviar la imagen en base64

                // Enviar la imagen al archivo PHP
                const response = await fetch(apiUrl, {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`Error HTTP! status: ${response.status}`);
                }

                const result = await response.json();
                return result;
            }

            // Variables para la cámara y el stream
            let cameraStream = document.getElementById('cameraStream');
            let stream;

            // Botones
            let openCameraButton = document.getElementById('openCameraButton');
            let closeCameraButton = document.getElementById('closeCameraButton');
            let recognizeButtonCamera = document.getElementById('recognizeButtonCamera');
            let cameraStatus = document.getElementById('cameraStatus');

            // Secciones de cámara e imagen
            let cameraSection = document.getElementById('cameraSection');
            let uploadSection = document.getElementById('uploadSection');

            // Función para abrir la cámara
            //--------------------------------------------------------------
            // Evento para capturar la foto
            capturePhotoButton.addEventListener('click', function() {
                let context = cameraCanvas.getContext('2d');
                cameraCanvas.width = cameraStream.videoWidth;
                cameraCanvas.height = cameraStream.videoHeight;
                context.drawImage(cameraStream, 0, 0, cameraCanvas.width, cameraCanvas.height);
                recognizeButtonCamera.disabled = false; // Habilita el botón de reconocimiento
            });
            // Evento para reconocer la foto tomada
            // Evento para reconocer la foto tomada
            recognizeButtonCamera.addEventListener('click', async function() {
                let base64Image = cameraCanvas.toDataURL('image/jpeg').split(',')[1]; // Extrae base64 sin encabezado

                try {
                    let result = await recognizeOrchid(base64Image); // Envía la imagen para reconocimiento
                    let output = "<h2>Resultados de identificación</h2>";

                    if (result.result.classification && result.result.classification.suggestions) {
                        result.result.classification.suggestions.forEach(suggestion => {
                            output += `<strong>Nombre de la planta:</strong> ${suggestion.name} 
                            <button class="btn btn-sm btn-info" onclick="copiarAlPortapapeles('${suggestion.name}')">Copiar</button><br>`;
                            output += `<strong>Probabilidad:</strong> ${(suggestion.probability * 100).toFixed(2)}%<br>`;

                            // Mostrar imágenes similares, si están disponibles
                            if (suggestion.similar_images) {
                                output += `<strong>Imágenes similares:</strong><br>`;
                                suggestion.similar_images.forEach(image => {
                                    output += `<a href='${image.url}' target='_blank'>
                                    <img src='${image.url}' alt='Imagen similar' style='width: 300px; height: auto;'></a><br>`;
                                });
                            }
                            output += "<hr>";
                        });
                    } else {
                        output += "<p>No se encontraron coincidencias.</p>";
                    }

                    // Mostrar el resultado en la interfaz
                    cameraStatus.innerHTML = output;

                } catch (err) {
                    cameraStatus.innerHTML = `<p class='text-danger'>Error al reconocer la orquídea: ${err.message}</p>`;
                }
            });
            //--------------------------------------------------------------
            // Función para cerrar la cámara
            closeCameraButton.addEventListener('click', function() {
                stream.getTracks().forEach(function(track) {
                    track.stop(); // Detiene todos los tracks del stream
                });
                cameraStream.srcObject = null; // Limpia el stream del video
                cameraStatus.innerHTML = "<p class='text-info'>Cámara desactivada.</p>";
                recognizeButtonCamera.disabled = true; // Desactiva el botón de reconocimiento
            });


            // Botones de selección de cámara o cargar imagen
            document.getElementById('useCameraButton').addEventListener('click', function() {
                cameraSection.style.display = 'block';
                uploadSection.style.display = 'none';
            });

            document.getElementById('uploadImageButton').addEventListener('click', function() {
                cameraSection.style.display = 'none';
                uploadSection.style.display = 'block';
            });

            // Funcionalidad para cargar una imagen
            document.getElementById('uploadInput').addEventListener('change', function(event) {
                let file = event.target.files[0];
                let validTypes = ["image/jpeg", "image/png", "image/jpg", "image/gif"];

                if (validTypes.includes(file.type)) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        let uploadedImage = document.getElementById('uploadedImage');
                        uploadedImage.src = e.target.result;
                        uploadedImage.style.display = 'block';

                        // Llama a la API (archivo PHP) para reconocer la imagen
                        recognizeOrchid(e.target.result.split(',')[1]) // Enviamos la imagen en base64 sin el encabezado "data:image/..."
                            .then(data => {
                                // Mostrar los resultados de la API
                                let output = "<h2>Resultados de identificación</h2>";

                                if (data.result.classification && data.result.classification.suggestions) {
                                    data.result.classification.suggestions.forEach(suggestion => {
                                        output += `<strong>Nombre de la planta:</strong> ${suggestion.name} <button class="btn btn-sm btn-info" onclick="copiarAlPortapapeles('${suggestion.name}')">Copiar</button><br>`;
                                        output += `<strong>Probabilidad:</strong> ${(suggestion.probability * 100).toFixed(2)}%<br>`;

                                        // Imágenes similares
                                        if (suggestion.similar_images) {
                                            output += `<strong>Imágenes similares:</strong><br>`;
                                            suggestion.similar_images.forEach(image => {
                                                output += `<a href='${image.url}' target='_blank'><img src='${image.url}' alt='Imagen similar' style='width: 300px; height: auto;'></a><br>`;
                                            });
                                        }
                                        output += "<hr>";
                                    });
                                } else {
                                    output += "<p>No se encontraron coincidencias.</p>";
                                }

                                document.getElementById('uploadStatus').innerHTML = output;
                            })
                            .catch(err => {
                                document.getElementById('uploadStatus').innerHTML = `<p class='text-danger'>Error al reconocer la orquídea: ${err.message}</p>`;
                            });

                        document.getElementById('recognizeButtonImage').style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    document.getElementById('uploadStatus').innerHTML = "<p class='text-danger'>Por favor, selecciona un archivo de imagen válido (JPEG, PNG, GIF).</p>";
                    event.target.value = ''; // Resetea el campo de carga de archivos Cmom
                }
            });
            // Función para abrir la cámara (con preferencia para la cámara trasera en dispositivos móviles)
openCameraButton.addEventListener('click', function() {
    // Detectar si el dispositivo es móvil
    const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);

    // Configuración para acceder a la cámara trasera si está disponible
    const constraints = {
        video: {
            facingMode: isMobile ? { exact: "environment" } : "user" // "environment" intenta abrir la cámara trasera en móviles
        }
    };

    navigator.mediaDevices.getUserMedia(constraints)
        .then(function(mediaStream) {
            stream = mediaStream;
            cameraStream.srcObject = stream;
            cameraStatus.innerHTML = "<p class='text-success'>Cámara activada.</p>";
            closeCameraButton.disabled = false;
            capturePhotoButton.disabled = false; // Habilita el botón de captura
        })
        .catch(function(error) {
            cameraStatus.innerHTML = `<p class='text-danger'>No se pudo acceder a la cámara: ${error.message}</p>`;
        });
});

        </script>
    </body>

    </html>