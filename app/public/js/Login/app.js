document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');

    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Impedir o envio padrão do formulário

            const loginElement = document.getElementById('nameEnter');
            const senhaElement = document.getElementById('passEnter');
            const nomeClienteElement = document.getElementById('nomeCliente');

            // Verificar se os elementos existem antes de acessar seus valores
            if (!loginElement || !senhaElement || !nomeClienteElement) {
                console.error('Um ou mais elementos necessários não foram encontrados no DOM.');
                return;
            }

            const login = loginElement.value;
            const senha = senhaElement.value;
            const nomeCliente = nomeClienteElement.value; // Obter o nome do cliente do campo oculto

            // Construir a URL dinamicamente com base no nome do cliente
            const apiUrl = `/RTKSistema/api/${encodeURIComponent(nomeCliente)}/login`;

            // Fazer a requisição AJAX para a API de login
            fetch(apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `login=${encodeURIComponent(login)}&senha=${encodeURIComponent(senha)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Redirecionar diretamente para o dashboard do cliente específico
                    window.location.href = data.redirect;
                } else {
                    alert(data.message); // Exibir mensagem de erro
                }
            })
            .catch(error => console.error('Erro ao fazer login:', error));
        });
    } else {
        console.error('Formulário de login não encontrado.');
    }

});

function verificarSessao() {
    const nomeClienteElement = document.getElementById('nomeCliente');
    if (!nomeClienteElement) {
        console.error('Elemento nomeCliente não encontrado no DOM.');
        return;
    }

    const nomeCliente = nomeClienteElement.value;

    fetch(`/RTKSistema/api/verificarSessao`)
        .then(response => {
            if (!response.ok) {
                window.location.href = `/${nomeCliente}/login`; // Redirecionar se a sessão não for válida
            }
        })
        .catch(error => console.error('Erro ao verificar sessão:', error));
}
