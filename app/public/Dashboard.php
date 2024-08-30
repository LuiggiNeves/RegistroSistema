

<script>
    document.addEventListener('DOMContentLoaded', function() {
    verificarToken(); // Verifique o token ao carregar a página
});

function verificarToken() {
    const token = localStorage.getItem('token');

    if (!token) {
        // Se o token não estiver presente, redirecionar para a página de login
        window.location.href = '/RTKSistema/{nomeCliente}/login'; // Substitua {nomeCliente} pelo valor dinâmico apropriado
    } else {
        // Verificar a validade do token no lado do cliente
        try {
            const payload = JSON.parse(atob(token.split('.')[1]));
            const exp = payload.exp;

            // Verificar se o token expirou
            if (exp < Date.now() / 1000) {
                alert('Sessão expirada. Faça login novamente.');
                localStorage.removeItem('token');
                window.location.href = '/RTKSistema/{nomeCliente}/login'; // Substitua {nomeCliente} pelo valor dinâmico apropriado
            } else {
                // Token válido, carregar o conteúdo protegido
                carregarConteudoProtegido();
            }
        } catch (e) {
            console.error('Token inválido', e);
            localStorage.removeItem('token');
            window.location.href = '/RTKSistema/{nomeCliente}/login'; // Substitua {nomeCliente} pelo valor dinâmico apropriado
        }
    }
}

function carregarConteudoProtegido() {
    const nomeCliente = '{nomeCliente}'; // Substitua {nomeCliente} pelo valor dinâmico apropriado

    fetchComToken(`/RTKSistema/${nomeCliente}/conteudo_protegido`) // Substitua pelo endpoint correto para carregar o conteúdo
        .then(response => response.text())
        .then(html => {
            document.getElementById('conteudoProtegido').innerHTML = html; // Carregar o conteúdo protegido na página
        })
        .catch(error => console.error('Erro ao carregar conteúdo protegido:', error));
}

function fetchComToken(url, options = {}) {
    const token = localStorage.getItem('token');
    console.log('Token a ser enviado:', token); // Log do token antes da requisição
    
    if (token) {
        options.headers = {
            ...options.headers,
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

</script>