class Search {
    setAjax() {
        let xhr = false;
        if (window.XMLHttpRequest) {
            xhr = new XMLHttpRequest();
        }
        else if (window.ActiveXObject) {
            try {
                xhr = new ActiveXObject("MSXML2.XMLHTTP");
            }
            catch (e) {
                try {
                    xhr = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch (e) {
                    xhr = false;
                }
            }
        }
        
        return xhr;
    };
    
    showSearch(table, dataJson) {
        let drop;
        let classProp;
        if (table == 'nombreAlumno') {
            drop = document.querySelector('#alumnoDrop .containerOpciones');
            classProp = "opcionAlumno";
        } else if (table == 'nombreProfesor') {
            drop = document.querySelector('#profesorDrop .containerOpciones');
            classProp = "opcionProfesor";
        } else if (table == 'nombreCurso') {
            drop = document.querySelector('#cursoDrop .containerOpciones');
            classProp = "opcionCurso";
        } else if (table == 'nombreDiplomatura') {
            drop = document.querySelector('#diplomaturaDrop .containerOpciones');
            classProp = "opcionDiplomatura";
        } else {
            return false;
        }
        if (dataJson) {
            let data = JSON.parse(dataJson);
            let value;
            let values = '';

            data.forEach(value => {
                value = `<p class="${classProp} opcion mt-2" aria-id="${value.id}">${value.nombre}</p>`;
                values += value;
            });

            drop.innerHTML = '';
            drop.innerHTML += values;
        } else {
            drop.innerHTML = '';
        }
    }
    
    getSearch(e) {
        const xhr = this.setAjax();
        const showSearch = (table, dataJson) => this.showSearch(table, dataJson);
        let data;
        
        if (!xhr) {
            data = false;
        } else {
            const value = e.value;
            const table = e.id;
            const dataToSend = `search_ajax=${value}&sender=${table}`;
            xhr.open('POST', './busquedas.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status=== 200) {
                    data = xhr.responseText;
                    showSearch(table, data);
                }
            }        
            
            xhr.send(dataToSend);
        }

    }
}