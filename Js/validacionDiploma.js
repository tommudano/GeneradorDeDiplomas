const UICtrlDiploma = (function() {
    const UISelectors = {
        input: "input",
        form: '#form',
        btn: '#btn',
        mes: '#mes',
        anio: '#anio'
    };

    return {
        getSelectors: () => {
            return UISelectors;
        }
    };
})();

const AppDiploma = (function(UICtrlDiploma) {
    const UISelectors = UICtrlDiploma.getSelectors();
    const validation = new Validation(UISelectors.input, UISelectors.btn);

    const loadEventListeners = () => {
        document.querySelector(UISelectors.mes).addEventListener('keyup', validarMes);

        document.querySelector(UISelectors.anio).addEventListener('keyup', validarAnio);

        document.body.addEventListener('click', validarId);
    };

    const validarMes = (e) => {
        const regExMes = /^([a-z\sÁÉÍÓÚñáéíóúÑ]{2,50})$/i;
        const val = regExMes.test(e.target.value);
        validation.validar(e.target, val, 'Ingrese un mes v&aacute;lido');
    };

    const validarAnio = (e) => {
        const regExAnio = /(?:(?:20|21)[0-9]{2})/g;
        const val = regExAnio.test(e.target.value);
        validation.validar(e.target, val, 'Ingrese un a&ntilde;o v&aacute;lido');
    }

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
        if (obj.classList.contains('opcionAlumno')) {
            id = await getId('#alumnoId');
        } else if (obj.classList.contains('opcionProfesor')) {
            id = await getId('#profesorId');
        } else if (obj.classList.contains('opcionCurso')) {
            id = await getId('#cursoId');
        } else {
            return false;
        }

        if (regExNum.test(id.value)) {
            validation.checkBeforeSubmit();
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

})(UICtrlDiploma);

document.addEventListener('DOMContentLoaded', AppDiploma.init);