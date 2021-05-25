const UICtrl = (function() {
    const UISelectors = {
        input: "input",
        form: '#form',
        btn: '#btn',
        usuario: '#usuario',
        pwd: '#pwd'
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
        document.querySelector(UISelectors.usuario).addEventListener('keyup', validarUsuario);

        document.querySelector(UISelectors.pwd).addEventListener('keyup', validarPwd);
    };

    const validarUsuario = (e) => {
        const regExNombre = /^(?![0-9]*$)[a-zA-Z0-9]{2,50}$/;
        const val = regExNombre.test(e.target.value);
        validation.validar(e.target, val, 'Ingrese un usuario v&aacute;lido');
    };

    const validarPwd = (e) => {
        const regExPwd = /^([\w\s\W]{8,})$/;
        const val = regExPwd.test(e.target.value);
        validation.validar(e.target, val, 'Ingrese una contrase&ntilde;a v&aacute;lida');
    };

    return {
        init: () => {
            validation.checkBeforeSubmit();
            loadEventListeners();
        }
    };

})(UICtrl);

document.addEventListener('DOMContentLoaded', App.init);