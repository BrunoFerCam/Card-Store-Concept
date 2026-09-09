# Card Store Concept — Portal Administrativo de Cartas

## Como inicializar o projeto

### Opção A — com Docker (recomendada)

Pré-requisito: Docker Desktop com o daemon em execução.

1. Suba os serviços (banco + servidor web). Na primeira vez o
   `database/schema.sql` é aplicado automaticamente na criação do banco:

   ```bash
   docker compose up -d --build
   ```

2. Popule os dados de exemplo (usuário de teste, cartas, edições e raridades):

   ```bash
   docker compose exec web php database/seed.php
   ```

3. Acesse `http://localhost:8000` e faça login.

Para parar: `docker compose down`. Para parar e apagar o banco:
`docker compose down -v`.

## Credenciais de teste

- Usuário: `admin`
- Senha: `admin123`

## Decisões de UX/produto

1. **Seleção de edição guiada pelo Card Game** — o campo de edição começa
   desabilitado e só é preenchido após a escolha do jogo. Isso evita
   combinações inválidas (ex.: edição de Pokémon em uma carta de Magic) e
   reduz erros de cadastro.
2. **Implementação de visualização do catálogo de cartas sem necessidade de Login** — o usuário não admin tem a capacidade de navegar e usar o sistema de filtros para observar as cartas já registradas. As funções de edição das cartas e categorias continuam exclusivas aos administradores logados.
3. **Diferentes temas** - o usuário tem a opção de escolher entre os temas diurno e noturno, com o objetivo de melhorar a experiência de uso.
4. **Menus em popup Modal** - todos os menus de edição e criação de cartas são feitos em Modal, criando uma experiência mais fluida para o administrador, integrando todas as edições á página e permitindo uma saída mais fácil.
5. **Single-Page design** - a tela de gerenciamento do administrador foi criada com uma experiência contínua em mente, contendo todos os menus e seções em apenas uma tela, criando uma organização e fluidez na experiência do usuário.

