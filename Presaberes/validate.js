
// Fetch all the forms we want to apply custom Bootstrap validation styles to
const forms = document.querySelectorAll('.needs-validation')

// Loop over them and prevent submission
Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
            window.scrollTo({
                top: document.querySelector('#cedula').offsetTop - 100,
                behavior: 'smooth'
            });
        }

        form.classList.add('was-validated')
    }, false)
})
function alertSuccessfull(type, msg) {
    if (type) {
        Swal.fire({
            title: "¡Exitoso!",
            text: msg,
            icon: "success", // Puedes usar: success, error, warning, info, question
            showConfirmButton: false,
            timer: 2000,
        });
    } else {
        Swal.fire({
            title: "¡Fallido!",
            text: msg,
            icon: "error", // Puedes usar: success, error, warning, info, question
            showConfirmButton: false,
            timer: 2000,
        });
    }
}
