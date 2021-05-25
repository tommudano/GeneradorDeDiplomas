const UICtrl = (function() {
    const UISelectors = {
        input: "input",
        form: '#form',
        btn: '#btn',
        nombre: '#nombre',
        apellido: '#apellido',
        dni: '#dni',
        firma: '#firma'
    };

    return {
        getSelectors: () => {
            return UISelectors;
        }
    };
})();

const App = (function(UICtrl) {
    const UISelectors = UICtrl.getSelectors();
    const validation = new Validation(UISelectors.input, UISelectors.btn);

    const loadEventListeners = () => {
        document.querySelector(UISelectors.dni).addEventListener('keyup', validarDNI);

        document.querySelector(UISelectors.nombre).addEventListener('keyup', validarNombreApellido);

        document.querySelector(UISelectors.apellido).addEventListener('keyup', validarNombreApellido);

        document.querySelector(UISelectors.firma).addEventListener('change', validarFirma);
    };

    const validarNombreApellido = (e) => {
        const regExNombre = /^([a-z\sÁÉÍÓÚñáéíóúÑ]{2,50})$/i;
        const val = regExNombre.test(e.target.value);
        validation.validar(e.target, val, 'Ingrese un nombre verdadero');
    };

    const validarDNI = (e) => {
        const regExNum = /^\d+$/;
        const val = regExNum.test(e.target.value);
        validation.validar(e.target, val, 'Ingrese un n\u00famero verdadero');
    };

    const validarFirma = (e) => {
        let file = e.target.files[0];
        const fileNameRegExp = /^\w*(\.(png)){1}$/i;
        const maxSize = 63000;
        try {
            const fileName = file['name'];
            const fileSize = file['size'];
            let val = fileNameRegExp.test(fileName);
            if (!val) {
                validation.validar(e.target, val, 'Utilize im&aacute;genes de extensi&oacute;n png');
            } else {
                val = maxSize >= fileSize;
                validation.validar(e.target, val, 'El archivo es muy pesado. Intente con uno m&aacute;s liviano');
            }
        } catch (e) {
            let error = e;
        }
    };

    return {
        init: () => {
            validation.checkBeforeSubmit();
            loadEventListeners();
        }
    };

})(UICtrl);

document.addEventListener('DOMContentLoaded', App.init);