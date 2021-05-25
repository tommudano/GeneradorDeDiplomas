const UICtrlCurso = (function() {
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

const AppCurso = (function(UICtrlCurso) {
    const UISelectors = UICtrlCurso.getSelectors();
    const validation = new Validation(UISelectors.input, UISelectors.btn);

    const loadEventListeners = () => {
        document.querySelector(UISelectors.nombre).addEventListener('keyup', validarNombre);

        document.body.addEventListener('click', validarId);
    };

    const validarNombre = (e) => {
        const regExNombre = /^([a-z\sÁÉÍÓÚñáéíóúÑ]{2,50})$/i;
        const val = regExNombre.test(e.target.value);
        validation.validar(e.target, val, 'Ingrese un nombre verdadero');
    };

    const getId = (id) => {
        return new Promise(resolve => {
            setTimeout(function() {
            idRet = document.querySelector(id);
            resolve(idRet);
            }, 250);
        });
    };

    const validarId = async (e) => {
        const regExNum = /^\d+$/;
        const obj = e.target;
        let id; 
        if (obj.classList.contains('opcionDiplomatura')) {
            id = await getId('#diplomaturaId');
        } else {
            return false;
        }

        if (regExNum.test(id.value)) {
            validation.checkBeforeSubmit();
            document.querySelector('#diplomaturaV').value = obj.textContent;
        } else {
            return false;
        }
    };

    return {
        init: () => {
            validation.checkBeforeSubmit();
            loadEventListeners();
        }
    };

})(UICtrlCurso);

document.addEventListener('DOMContentLoaded', AppCurso.init);