const tap = document.querySelector('.profile');
  tap.addEventListener('click', function(){
       const toggleMenu = document.querySelector('.menu');
  toggleMenu.classList.toggle('active');
});


//form

document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.filter-button');
    const submitButton = document.getElementById('submitButton');
    //const externalInput = document.getElementById('externalInput');

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            button.classList.toggle('active'); // Alternar clase 'active' al hacer clic
            // Llamar a la función para mostrar los datos recopilados
            displayFormData();
        });
    });

    submitButton.addEventListener('click', () => {
        // Llamar a la función para mostrar los datos recopilados
        displayFormData();
    });

    function displayFormData() {
        const selectedItems = [];
        buttons.forEach(button => {
            if (button.classList.contains('active')) {
                selectedItems.push(button.textContent.trim()); // Agregar el texto del botón seleccionado
            }
        });

        //const externalValue = externalInput.value.trim(); // Obtener el valor del campo de texto

        let message = 'Elementos seleccionados:\n';
        selectedItems.forEach(item => {
            message += `- ${item}\n`;
        });
        //message += `\nValor del campo externo: ${externalValue}`;

        alert(message); // Mostrar los datos recopilados en un alert
    }
});

