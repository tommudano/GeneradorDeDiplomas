const UICtrl = (function() {
    const UISelectors = {
        input: "input",
        form: '#form',
        btn: '#btn',
        busqueda: '.busqueda'
    };

    return {
        getSelectors: () => {
            return UISelectors;
        }
    };
})();

const App = (function(UICtrl) {
    const UISelectors = UICtrl.getSelectors();  
    const search = new Search();

    const loadEventListeners = () => {
        document.querySelectorAll(UISelectors.busqueda).forEach(busqueda => busqueda.addEventListener('keyup', searchData));

        document.body.addEventListener('click', saveData);
    };
    
    const searchData = (e) => {
        if (validarFormatoBusqueda(e.target.value)) {
            search.getSearch(e.target);
        } else {
            e.target.nextElementSibling.innerHTML = '';
        }
    };

    const validarFormatoBusqueda = (busqueda) => {
        const regExBusqueda = /^([a-z\sÁÉÍÓÚñáéíóúÑ]{1,50})$/i;
        const val = regExBusqueda.test(busqueda);
        return val;
    };

    const selectData = (obj) => {
        let btn = obj.parentElement.parentElement.previousElementSibling;
        btn.textContent = obj.textContent;
    };

    const saveData = (e) => {
        const obj = e.target;
        let id; 
        if (obj.classList.contains('opcionAlumno')) {
            id = obj.attributes['aria-id'].value;
            saveDataAlumno(id);
            selectData(obj);
        } else if (obj.classList.contains('opcionProfesor')) {
            id = obj.attributes['aria-id'].value;
            saveDataProfesor(id);
            selectData(obj);
        } else if (obj.classList.contains('opcionCurso')) {
            id = obj.attributes['aria-id'].value;
            saveDataCurso(id);
            selectData(obj);
        } else if (obj.classList.contains('opcionDiplomatura')) {
            id = obj.attributes['aria-id'].value;
            saveDataDiplomatura(id);
            selectData(obj);
        } else if (obj.classList.contains('opcionDiplomaturaV')) {
            id = obj.attributes['aria-id'].value;
            saveDataDiplomaturaV(id);
            selectData(obj);
        }
    };

    const saveDataAlumno = (id) => {
        let input = document.querySelector('#alumnoId');
        input.value = id;
    };

    const saveDataDiplomatura = (id) => {
        let input = document.querySelector('#diplomaturaId');
        input.value = id;
    };

    const saveDataDiplomaturaV = (id) => {
        let input = document.querySelector('#diplomaturaIdV');
        input.value = id;
    };

    const saveDataProfesor = (id) => {
        let input = document.querySelector('#profesorId');
        input.value = id;
    };

    const saveDataCurso = (id) => {
        let input = document.querySelector('#cursoId');
        input.value = id;
    };

    return {
        init: () => {
            loadEventListeners();
        }
    };

})(UICtrl);

document.addEventListener('DOMContentLoaded', App.init);