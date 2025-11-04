<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quem está assistindo?</title>
    <style>
        body {
            background-color: #141414;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }
        .profiles-container {
            text-align: center;
        }
        .profiles-container h1 {
            font-size: 3.5vw;
        }
        /* Estilo para a lista de perfis */
        .profile-list {
            display: flex;
            gap: 20px; /* Espaçamento entre os perfis */
            justify-content: center;
            padding: 50px 0;
            background-color: #141414; /* Cor de fundo escura */
        }

        /* Link do Perfil */
        .profile-list a {
            text-decoration: none; /* Remove sublinhado */
            color: inherit;
        }

        /* Container de cada Perfil */
        .profile {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        /* Efeito de hover (Zoom in) */
        .profile:hover {
            transform: scale(1.1);
        }

        /* O Círculo de Iniciais (Substitui a imagem) */
        .profile-initials {
            width: 150px; /* Tamanho do ícone */
            height: 150px;
            border-radius: 4px; /* Levemente arredondado, como o Netflix */
            margin-bottom: 10px;

            display: flex;
            justify-content: center;
            align-items: center;

            color: #FFFFFF;
            font-size: 3em;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.4);

            border: 3px solid transparent; /* Borda transparente inicial */
            transition: border-color 0.3s ease;
        }

        /* Borda branca ao passar o mouse */
        .profile:hover .profile-initials {
            border-color: #f3f3f3;
        }

        /* Nome do Perfil */
        .profile-name {
            color: #AAAAAA;
            font-size: 1.1em;
            text-align: center;
            transition: color 0.3s ease;
        }

        /* Nome fica mais claro ao passar o mouse */
        .profile:hover .profile-name {
            color: #f3f3f3;
        }
    </style>
</head><body>
<div class="profiles-container">
    <h1>Quem está assistindo?</h1>
    <div class="profile-list">
        <a href="<?= base_url('?id=1') ?>">
            <div class="profile" data-name="Matheus Passos">
                <span class="profile-initials"></span>
                <div class="profile-name">Matheus</div>
            </div>
        </a>
        <a href="<?= base_url('?id=2') ?>">
            <div class="profile" data-name="Isabele Passos">
                <span class="profile-initials"></span>
                <div class="profile-name">Isa</div>
            </div>
        </a>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const profiles = document.querySelectorAll('.profile');

        // Lista de cores de fundo (Estilo Netflix)
        const backgroundColors = [
            '#E50914', // Vermelho (Principal)
            '#00A8E1', // Azul Claro
            '#3B5998', // Azul Escuro (Facebook style)
            '#56B84A', // Verde
            '#FFC000', // Amarelo
            '#A062C9', // Roxo
            '#FF5B00'  // Laranja
        ];

        /**
         * Função para extrair as iniciais de um nome.
         * Pega a primeira letra do primeiro nome e a primeira letra do último nome (se houver).
         */
        function getInitials(name) {
            // Remove espaços extras e divide o nome em partes
            const parts = name.trim().split(/\s+/);

            let initials = '';

            if (parts.length > 0) {
                // Pega a primeira letra do primeiro nome
                initials += parts[0].charAt(0);
            }

            if (parts.length > 1) {
                // Pega a primeira letra do último nome
                initials += parts[parts.length - 1].charAt(0);
            } else if (parts.length === 1 && initials.length < 2) {
                // Se houver apenas um nome, usa a primeira letra duas vezes (ex: "A" se torna "AA")
                initials += initials.charAt(0);
            }

            // Se o nome for vazio ou muito curto, retorna um fallback
            return initials.length > 0 ? initials.toUpperCase() : '??';
        }

        /**
         * Função simples de hash para gerar um índice de cor consistente
         * baseado no nome completo.
         */
        function generateColorIndex(name) {
            let hash = 0;
            for (let i = 0; i < name.length; i++) {
                hash = name.charCodeAt(i) + ((hash << 5) - hash);
            }
            return Math.abs(hash) % backgroundColors.length;
        }

        profiles.forEach(profile => {
            const initialsElement = profile.querySelector('.profile-initials');
            const fullName = profile.getAttribute('data-name'); // Usa o nome completo para cor e iniciais

            if (initialsElement && fullName) {
                // 1. Calcula as iniciais
                const initials = getInitials(fullName);

                // 2. Aplica o conteúdo das iniciais
                initialsElement.textContent = initials;

                // 3. Calcula e aplica a cor de fundo (usando o nome completo para consistência)
                const colorIndex = generateColorIndex(fullName);
                const color = backgroundColors[colorIndex];

                initialsElement.style.backgroundColor = color;
            }
        });
    });
</script>
</body>
</html>