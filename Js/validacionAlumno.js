const UICtrl = (function() {
    const UISelectors = {
        input: "input",
        form: '#form',
        btn: '#btn',
        dni: '#dni',
        nombre: '#nombre',
        apellido: '#apellido'
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

    return {
        init: () => {
            validation.checkBeforeSubmit();
            loadEventListeners();
        }
    };

})(UICtrl);

document.addEventListener('DOMContentLoaded', App.init);