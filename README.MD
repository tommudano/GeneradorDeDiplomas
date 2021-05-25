# Generador de Diplomas
Esta es una aplicación basada en PHP para la generación de Diplomas de finalización de Cursos.
Esta aplicación utiliza las APIs de [php-qrcode](https://github.com/chillerlan/php-qrcode "API de generación de códigos QR") de [Chillerlan](https://github.com/chillerlan "Creador de la API anterior") y [TCPDF](https://github.com/tecnickcom/TCPDF "API de generación de archivos .PDF") de [Tecnickcom](https://github.com/tecnickcom "Creador de la API anterior").
## Requerimientos
* PHP 7.4+
## Aclaraciones previas
La aplicación utiliza como base de datos MariaDB. Se incluye un archivo (dbDiplomas.sql) el archivo para generar la base de datos (cuyo nombre, por default deberá ser _diplomas_). El archivo de conexión a la base de datos se encuentra en core/init.php
Se generarán dos usuarios demostrativos:
* Admin (pass: admin123456), con permisos de adminsitrador
* Standard (pass: standard), sin permisos de adminsitrador
La diferencia entre usuarios son los permisos de eliminación, edición y creación concedidos al administrador.
La imagen de fondo del diploma, por defecto, es un fondo blanco. Esto puede cambiarse reeplazando la imagen diploma.png (en la raíz del proyecto) por la deseada bajo el nombre _diploma.png_.
Para modificar el posicionamiento de los datos, o el formato bajo el que se presentan, referirse al archivo _diploma.php_ (en la raíz del proyecto).
## Funcionamiento
Los dimplomas son creados por los usuarios registrados (solo el administrador puede registrar usuarios).

Para ello se crean previamente las intancias de alumnos, cursos, profesores y diplomaturas (categoría del curso) necesarias.
Los diplomas quedan firmados con la firma digital del porfesor o, en caso de no cargarla, sin firmar.
Se envía a los estudiantes el link público de acceso al diploma (_.../preDiploma.php?id=..._). Allí se tiene el link de descarga del diploma (en formato .PDF).
Dentro del diploma, además de los datos, se encuentra un código QR que redirige a la sección de verificación del diploma, donde se valida que ese diploma fue realmente creado por la organización a la que dice pertenecer.