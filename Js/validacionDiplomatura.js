const UICtrl = (function() {
    const UISelectors = {
        input: "input",
        form: '#form',
        btn: '#btn',
        nombre: '#nombre'
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
        document.querySelector(UISelectors.nombre).addEventListener('keyup', validarNombre);
    };

    const validarNombre = (e) => {
        const regExNombre = /^([a-z\sÁÉÍÓÚñáéíóúÑ]{2,50})$/i;
        const val = regExNombre.test(e.target.value);
        validation.validar(e.target, val, 'Ingrese un nombre verdadero');
    };

    return {
        init: () => {
            validation.checkBeforeSubmit();
            loadEventListeners();
        }
    };

})(UICtrl);

document.addEventListener('DOMContentLoaded', App.init);