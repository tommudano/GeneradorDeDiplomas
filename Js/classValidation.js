class Validation {
    constructor(input, btn) {
        this.input = input;
        this.btn = btn;
    }

    checkBeforeSubmit() {
        const inputErrorCheck = document.querySelectorAll(this.input);
        let sinErrores = true;
        inputErrorCheck.forEach(input => {
            let errores = 0;
            if (input.classList.contains('is-invalid') || (input.value == "" && !input.classList.contains('noObligatorio'))) {
                errores++;
            }

            if (errores > 0) {
                sinErrores = false;
            }
        });
        
        if (sinErrores) {
            document.querySelector(this.btn).disabled = false;
        } else {
            document.querySelector(this.btn).disabled = true;
        }
    }

    validar(e, val, msj) {
        if (e.value === '') {
            e.classList.remove('is-valid');
            e.classList.remove('is-invalid');
        } else if (val) {
            e.classList.add('is-valid');
            e.classList.remove('is-invalid');
        } else {
            e.classList.add('is-invalid');
            e.classList.remove('is-valid');
        }

        this.checkBeforeSubmit();
    };
}