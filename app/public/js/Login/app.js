document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Impedir o envio padrão do formulário

    const login = document.getElementById('nameEnter').value;
    const senha = document.getElementById('passEnter').value;
    const id_empresa = document.getElementById('idEmpresa').value;
    const nomeCliente = document.getElementById('nomeCliente').value; // Obter o nome do cliente do campo oculto

    // Fazer a requisição AJAX para a API de login
    fetch('/RTKSistema/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `login=${encodeURIComponent(login)}&senha=${encodeURIComponent(senha)}&id_empresa=${encodeURIComponent(id_empresa)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            // Armazenar o token JWT no localStorage
            localStorage.setItem('token', data.token);
            // Redirecionar para o dashboard do cliente específico usando fetchComToken
            fetchComToken(`/RTKSistema/${nomeCliente}/dashboard`)
            .then(response => {
                if (response.ok) {
                    window.location.href = `/RTKSistema/${nomeCliente}/dashboard`;
                } else {
                    alert('Falha na autenticação ao redirecionar.');
                }
            })
            .catch(error => console.error('Erro ao redirecionar:', error));
        } else {
            alert(data.message); // Exibir mensagem de erro
        }
    })
    .catch(error => console.error('Erro ao fazer login:', error));
});

function fetchComToken(url, options = {}) {
    const token = localStorage.getItem('token');
    console.log('Token a ser enviado:', token); // Log do token antes da requisição
    
    if (token) {
        options.headers = {
            ...options.headers, // Preserva quaisquer outros cabeçalhos existentes
            'Authorization': `Bearer ${token}` // Adiciona o token JWT ao cabeçalho
        };
    }

    console.log('Options com Headers:', options); // Log para verificar as opções de headers
    return fetch(url, options)
        .then(response => {
            console.log('Response status:', response.status); // Log do status da resposta
            return response;
        })
        .catch(error => console.error('Erro na requisição com token:', error));
}


