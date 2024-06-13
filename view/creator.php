<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../controller/projectcontroller.php';
require_once '../controller/usercontroller.php';
session_start();
// Función para redirigir al usuario a la página de inicio de sesión de GitHub
function redirectToGitHubLoginPage() {
  $login_url = "https://github.com/login/oauth/authorize?client_id=4ca87aec846e119258f4";
  header("Location: $login_url");
  exit();
}

// Verificar si ya tenemos un token de acceso en la sesión
if (isset($_SESSION['github_access_token'])) {
} elseif (isset($_GET['code'])) {
    // Si recibimos un código de autorización de GitHub, intercambiamos por un token de acceso
    exchangeCodeForAccessToken($_GET['code']);
} else {
    // Si no hay un token de acceso, redirigir al usuario a la página de inicio de sesión de GitHub
    redirectToGitHubLoginPage();
}
$userInfo = getUserInfo();

$userId = $userInfo['id'];
$avatarUrl = $userInfo['avatar_url'];
$username = $userInfo['login'];
$name = $userInfo['name'];
$location = $userInfo['location'];
$publicRepos = $userInfo['public_repos'];
$publicGists = $userInfo['public_gists'];
$followers = $userInfo['followers'];
$following = $userInfo['following'];
$type = $userInfo['type'];
$createdAt = $userInfo['created_at'];
$updatedAt = $userInfo['updated_at'];
$planName = $userInfo['plan']['name'];

include '../config/config.php';
include '../controller/encrypt.php';

if (isset($_GET['data'])) {
    $encryptedData = $_GET['data'];
    $decryptedData = decryptData($encryptedData, SECRET_KEY);
}
if (isset($decryptedData)) {
  // Usar explode para dividir la cadena por comas
$dataArray = explode(',', $decryptedData);

// Acceder a los valores individuales
$repoId = $dataArray[0];
$repoName = $dataArray[1];
$ownerId = $dataArray[2];
} else {
  echo "No se recibieron datos o los datos no se pudieron desencriptar.";
}
// Definir la función que maneja las opciones
function manejarOpciones($opcion, $numero) {
    return "<div class=\"$opcion\"><h1>La palabra generada es....</h1><p>$opcion</p><p>$numero</p></div>";
}
// Función para obtener la información del usuario desde la API de GitHub
function getUserInfo() {
  // Obtener el token de acceso de la sesión
  $accessToken = $_SESSION['github_access_token'];

  // Construir la URL para obtener la información del usuario desde la API de GitHub
  $userUrl = "https://api.github.com/user";

  // Inicializar la solicitud CURL
  $ch = curl_init();

  // Establecer las opciones para la solicitud CURL
  curl_setopt($ch, CURLOPT_URL, $userUrl);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
      'Accept: application/vnd.github.v3+json', // Indica la versión de la API que queremos usar
      'Authorization: Bearer ' . $accessToken, // Incluye el token de acceso en el encabezado de autorización
      'User-Agent: My-App' // Puedes especificar el nombre de tu aplicación aquí
  ]);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Indica que queremos recibir la respuesta como una cadena

  // Ejecutar la solicitud CURL y obtener la respuesta
  $response = curl_exec($ch);

  // Verificar si la solicitud fue exitosa
  if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) {
      curl_close($ch);
      return null; // Retorna null si hubo un error al obtener la información del usuario
  }

  // Decodificar la respuesta JSON en un array asociativo
  $userInfo = json_decode($response, true);

  // Cerrar la sesión CURL
  curl_close($ch);

  return $userInfo; // Retorna los datos del usuario obtenidos de la API de GitHub
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link href='https://fonts.googleapis.com/css?family=Inter' rel='stylesheet'>
</head>

<body>
  <div class="left">
    <h2><span>Design</span><span style="color: #d14cff">Code</span></h2>

    <div class="form-container">
      <div class="progress-bar">
        <div class="progress"></div>
      </div>
      <form id="multiStepForm">
        <div class="form-step active">
          <h3>Paso 1 <span style="color: #fff6">de 4</span></h3>
          <h2>Descripción</h2>
          <textarea id="description" name="description" oninput="updatePreview()" placeholder="Añade una descripción al proyecto"></textarea>
        </div>


        <div class="form-step">
          <h3>Paso 2 <span style="color: #fff6">de 4</span></h3>
          <h2>Añade los requisitos de tu proyecto</h2>
          <div class="requirement-container">
            <div id="requirements"></div>
            <input type="text" id="requirement-input" name="requirements" placeholder="Añadir un requisito" oninput="updatePreview()" maxlength="20">
          </div>
        </div>
        
        <div class="form-step">
          <h3>Paso 3 <span style="color: #fff6">de 4</span></h3>
          <h2>Añade las categorías a tu proyecto</h2>
          <div class="tag-container">
            <div id="tags"></div>
            <input type="text" id="tag-input" name="tags" placeholder="Añadir una etiqueta" onkeyup="updatePreview()" maxlength="20">
          </div>
        </div>
        
        

        <div class="form-step">
          <h3>Paso 4 <span style="color: #fff6">de 4</span></h3>
          <h2>Añade las categorías a tu proyecto</h2>
          <input type="file" id="images" name="images[]" multiple>
        </div>
        

        <div class="buttons">
          <button type="button" id="prevBtn" onclick="nextPrev(-1)" disabled>Anterior</button>
          <button type="button" id="nextBtn" onclick="nextPrev(1)">Siguiente</button>
        </div>

        


        <input type="hidden" name="repoId" value="<?php echo $repoId;?>">
        <input type="hidden" name="name" value="<?php echo $repoName;?>">
        <input type="hidden" name="ownerId" value="<?php echo $ownerId;?>">

      </form>
    </div>
  </div>

  <div class="right">
    <div class="card">
      <header class="cardHeader">
        <h2 id="previewTitle"><?php echo $repoName;?></h2>
        <div>
          <div><img src="<?php echo $avatarUrl;?>" alt="" srcset="" width="30px" height="30px">
            <p id="user"><?php echo $username;?></p>
          </div>

          <div class="dialogbuttons"><a href=""><button class="git">Abrir en<br>Github <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"><g fill="none"><path d="M24 0v24H0V0zM12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035c-.01-.004-.019-.001-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022m-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="#ffffff" d="M7.024 2.31a9.08 9.08 0 0 1 2.125 1.046A11.432 11.432 0 0 1 12 3c.993 0 1.951.124 2.849.355a9.08 9.08 0 0 1 2.124-1.045c.697-.237 1.69-.621 2.28.032c.4.444.5 1.188.571 1.756c.08.634.099 1.46-.111 2.28C20.516 7.415 21 8.652 21 10c0 2.042-1.106 3.815-2.743 5.043a9.456 9.456 0 0 1-2.59 1.356c.214.49.333 1.032.333 1.601v3a1 1 0 0 1-1 1H9a1 1 0 0 1-1-1v-.991c-.955.117-1.756.013-2.437-.276c-.712-.302-1.208-.77-1.581-1.218c-.354-.424-.74-1.38-1.298-1.566a1 1 0 0 1 .632-1.898c.666.222 1.1.702 1.397 1.088c.48.62.87 1.43 1.63 1.753c.313.133.772.22 1.49.122L8 17.98a3.986 3.986 0 0 1 .333-1.581a9.455 9.455 0 0 1-2.59-1.356C4.106 13.815 3 12.043 3 10c0-1.346.483-2.582 1.284-3.618c-.21-.82-.192-1.648-.112-2.283l.005-.038c.073-.582.158-1.267.566-1.719c.59-.653 1.584-.268 2.28-.031Z"/></g></svg></button></a>

            <a href=""><button class="kanban">Abrir en<br>KanBan <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16"><path fill="#ffffff" d="M2.5 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zm5 2h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1m-5 1a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1zm9-1h1a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1"/></svg></button></a>
          </div>
        </div>
      </header>
      <img id="cover" src="https://www.pngall.com/wp-content/uploads/8/Sample.png" alt="" srcset="" width="100%" height="250px">
      <main class="cardBody">
        <footer class="cardFooter">
          <div class="tags">
            <div class="tag">
              <span id="tag1">Design</span>
            </div>
            <div class="tag">
              <span id="tag2">Code</span>
            </div>
            <div class="tag">
              <span id="tag3">Procreate</span>
            </div>
            <div class="tag">
              <span id="tag4">Figma</span>
            </div>
          </div>
  
        </footer>
        <h2>Descripción</h2>
        <p id="previewDescription">Lorem ipsum dolor sit amet consectetur adipisicing elit. Accusantium libero aliquam quaerat molestias eum nisi. Obcaecati, quas? Dolorem, excepturi quaerat.<br></p> 
      </main>


    </div>
  </div>

  

  <script>
// Función para manejar la carga de imágenes
function handleImageUpload(event) {
  const file = event.target.files[0];
  const reader = new FileReader();

  reader.onload = function(e) {
    const coverImage = document.getElementById('cover');
    coverImage.src = e.target.result;
  };

  if (file) {
    reader.readAsDataURL(file);
  }
}

// Event listener para el input de imágenes
const imageInput = document.getElementById('images');
imageInput.addEventListener('change', handleImageUpload);


    // Progreso y navegación
    let currentStep = 0;
    showStep(currentStep);

    function showStep(n) {
  const steps = document.getElementsByClassName("form-step");
  
  // Ocultar todos los pasos y remover la clase 'active' de todos
  for (let step of steps) {
    step.style.display = "none";
    step.classList.remove("active");
  }
  
  // Mostrar el paso actual y añadir la clase 'active'
  steps[n].style.display = "block";
  steps[n].classList.add("active");

  // Actualizar botones de navegación y barra de progreso
  document.getElementById("prevBtn").disabled = n === 0;
  if (n === steps.length - 1) {
    document.getElementById("nextBtn").innerHTML = "Enviar";
  } else {
    document.getElementById("nextBtn").innerHTML = "Siguiente";
  }
  updateProgressBar(n);
}


    function nextPrev(n) {
      const steps = document.getElementsByClassName("form-step");
      if (n === 1 && !validateForm()) return false;
      steps[currentStep].style.display = "none";
      currentStep += n;
      if (currentStep >= steps.length) {
        showAlert();
        sendDataToServer();
        return false;
      }
      showStep(currentStep);
    }

    function updateProgressBar(n) {
      const progress = document.getElementsByClassName("progress")[0];
      const steps = document.getElementsByClassName("form-step").length;
      const progressWidth = (n / (steps - 1)) * 100;
      progress.style.width = progressWidth + "%";
    }

    // Validación del formulario
    function validateForm() {
      const activeStep = document.querySelector('.form-step.active');
      const inputs = activeStep.querySelectorAll('input, textarea');

      if (currentStep === 2 && !tagContainer.querySelector('.tag')) {
        alert('Por favor, añada al menos una etiqueta.');
        return false;
      }

      if (currentStep === 1 && !requirementContainer.querySelector('.requirement')) {
        alert('Por favor, añada al menos un requisito.');
        return false;
      }

      for (let input of inputs) {
        if (input.type !== 'text' && input.value === '') {
          alert('Por favor, complete todos los campos.');
          return false;
        }
      }

      return true;
    }

    // Elementos para el manejo de etiquetas
    const tagInput = document.getElementById('tag-input');
    const tagContainer = document.getElementById('tags');

    tagInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && tagInput.value !== '') {
        e.preventDefault();
        addTag(tagInput.value);
        tagInput.value = '';
      }
    });

    // Array para almacenar los textos de las etiquetas existentes
    const existingTagTexts = [];

    function addTag(text) {
      if (document.querySelectorAll('#tags .tag').length >= 20) {
        alert('Solo se pueden agregar hasta 20 etiquetas.');
        return;
      }

      // Verificamos si el texto ya está presente en el array
      if (existingTagTexts.includes(text.trim())) {
        alert('Esta etiqueta ya ha sido agregada.');
        return;
      }

      // Agregamos el texto al array de textos de etiquetas existentes
      existingTagTexts.push(text.trim());

      const tag = document.createElement('div');
      tag.classList.add('tag');
      tag.textContent = text;

      const removeButton = document.createElement('span');
      removeButton.textContent = ' x';
      removeButton.style.cursor = 'pointer';
      removeButton.addEventListener('click', () => {
        // Al eliminar la etiqueta, también la eliminamos del array de textos
        const index = existingTagTexts.indexOf(text.trim());
        if (index !== -1) {
          existingTagTexts.splice(index, 1);
        }
        tagContainer.removeChild(tag);
      });

      tag.appendChild(removeButton);
      tagContainer.appendChild(tag);
    }

    // Elementos para el manejo de los requisitos
    const requirementInput = document.getElementById('requirement-input');
    const requirementContainer = document.getElementById('requirements');

    requirementInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && requirementInput.value !== '') {
        e.preventDefault();
        addRequirement(requirementInput.value);
        requirementInput.value = '';
      }
    });

    // Array para almacenar los textos de los requisitos existentes
    const existingRequirementTexts = [];

    function addRequirement(text) {
      if (document.querySelectorAll('#requirements .requirement').length >= 20) {
        alert('Solo se pueden agregar hasta 20 requisitos.');
        return;
      }

      // Verificamos si el texto ya está presente en el array
      if (existingRequirementTexts.includes(text.trim())) {
        alert('Este requisito ya ha sido agregado.');
        return;
      }

      // Agregamos el texto al array de textos de requisitos existentes
      existingRequirementTexts.push(text.trim());

      const requirement = document.createElement('div');
      requirement.classList.add('requirement');
      requirement.textContent = text;

      const removeButton = document.createElement('span');
      removeButton.textContent = ' x';
      removeButton.style.cursor = 'pointer';
      removeButton.addEventListener('click', () => {
        // Al eliminar el requisito, también lo eliminamos del array de textos
        const index = existingRequirementTexts.indexOf(text.trim());
        if (index !== -1) {
          existingRequirementTexts.splice(index, 1);
        }
        requirementContainer.removeChild(requirement);
      });

      requirement.appendChild(removeButton);
      requirementContainer.appendChild(requirement);
    }

    // Función para mostrar una alerta con los datos del formulario
    function showAlert() {
  const form = document.getElementById('multiStepForm');
  const formData = new FormData(form);
  let message = 'Datos del formulario:\n';

  // Recorre todos los campos del formulario
  for (let [key, value] of formData.entries()) {
    message += `${key}: ${value}\n`;
  }
  // Agregar los tags al mensaje
  if (existingTagTexts.length > 0) {
    message += 'Tags:\n';
    existingTagTexts.forEach(tag => {
      message += `- ${tag}\n`;
    });
  }

  // Muestra el mensaje en un alert
  alert(message);
}
function sendDataToServer() {
  // Obtener los datos del formulario
  const form = document.getElementById('multiStepForm');
  const formData = new FormData(form);

  // Agregar los tags al formData
  existingTagTexts.forEach((tag, index) => {
    formData.append(`tags[]`, tag); // Cambiar para enviar como un array de tags
  });

  // Crear una instancia de XMLHttpRequest
  const xhr = new XMLHttpRequest();

  // Especificar el método y la URL del script PHP
  xhr.open('POST', '../controller/projectcreator.php', true);

  // Configurar el evento onload para manejar la respuesta del servidor
  xhr.onload = function () {
    if (xhr.status >= 200 && xhr.status < 300) {
      // Éxito: La solicitud fue exitosa
      console.log('Respuesta del servidor:', xhr.responseText);
      document.getElementById('respuesta').innerHTML = xhr.responseText;
    } else {
      // Error: La solicitud falló
      console.error('Error al enviar los datos.');
    }
  };

  // Configurar el evento onerror para manejar errores de red
  xhr.onerror = function () {
    console.error('Error de red al enviar los datos.');
  };

  // Enviar los datos del formulario y los tags al servidor
  xhr.send(formData);
}



    // Función para actualizar la vista previa en tiempo real
    // Función para actualizar la vista previa en tiempo real
// Función para actualizar la vista previa en tiempo real
function updatePreview() {
  const tagElements = document.querySelectorAll('#tags .tag');
  const tagSpans = document.querySelectorAll('.cardFooter .tag span');

  tagSpans.forEach((span, index) => {
    if (tagElements[index]) {
      span.textContent = tagElements[index].textContent.replace(' x', '');
      span.parentElement.style.display = 'inline-block';
    } else {
      span.parentElement.style.display = 'none';
    }
  });
  const previewTitle = document.getElementById('previewTitle');
  const previewDescription = document.getElementById('previewDescription');
  const titleInput = document.getElementById('title');
  const descriptionInput = document.getElementById('description');
  
  if (titleInput) {
    previewTitle.textContent = titleInput.value;
  }
  
  let descriptionText = '';
  if (descriptionInput) {
    descriptionText = descriptionInput.value;
  }

  // Añadir los requisitos a la descripción
  const requirementsText = existingRequirementTexts.join('\n');
  if (requirementsText) {
    descriptionText += '<br>\n\nRequisitos:\n<ul>';
    existingRequirementTexts.forEach(req => {
      descriptionText += `<li>${req}</li>`;
    });
    descriptionText += '</ul>';
  }

  previewDescription.innerHTML = descriptionText;


}


  </script>
</body>
<link rel="stylesheet" href="../styles/creator.css">
</html>
