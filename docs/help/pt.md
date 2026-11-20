# Português · Eurowebcart CMS Light

- [Gerenciamento](pt/readme.md)
- [Perguntas frequentes sobre administração](pt/faq.md)
- [Modelos e componentes](pt/templates.md)

## Botões no cabeçalho da administração · 1.6.4

O globo abre o site e a seta termina a sessão. No telemóvel, só aparecem os ícones. Passe o cursor ou utilize o teclado para ver os respetivos rótulos.

Conteúdo, Formulários e pedidos, e Site e serviços abrem diretamente. Os separadores da secção escolhida aparecem abaixo do menu principal.

Insira [bread-crumbs] ou [search] no HTML da página inicial (index.php) ou de um modelo. Cada bloco aparece uma vez na posição escolhida. As definições do caminho de navegação e da pesquisa continuam ativas. [privacy] liga a /privacy/. Edite a política em Páginas.

```text
[bread-crumbs]
[search]
[privacy]
```


## Novo na versão 1.6.3

### Política de casa e privacidade

Abra “Modelos de Design” para editar a página inicial e a página /privacy.php. O código [page-content] exibe o conteúdo da página principal. Na política [site-name] e [site-url] levam o nome e endereço público de “Configurações → Geral”. Complete o texto inicial com informações sobre seu site e tratamento de dados.

### Cookie e voltar ao topo

No rodapé, [conf-policy] fornece um link para a política, [cookies] abre as configurações e [top] rola para o topo da página. Análises e mapas CMS integrados aguardam acordo. Os scripts adicionados manualmente a modelos ou cabeçalhos de serviço requerem configuração própria.

### Editor functions.php

Abra “Modelos de Design → Funções” após a guia “Cabeçalhos de Serviço”. Adicione fechamentos ao array retornado. No seu modelo PHP, chame `translit("Москва")` ou `ew_call("translit", "München")`. Em HTML insira [func=translit value="Москва и München"]. O exemplo produz `Moskva i Muenchen`.

### Editando modelos padrão

No campo “Design Templates” já contém o código fonte PHP e HTML com destaque. Ao editar um modelo padrão, selecione “PHP personalizado”. Salvar cria uma versão personalizada. Usando os botões próximos ao modelo, você pode desfazer a última edição ou restaurar o original.

### Verificação do PHP antes de salvar

Modelo de design, módulo e editores functions.php verificam a sintaxe com o comando `php -l`. Se houver um erro, a versão anterior permanece. Para selecionar a CLI do PHP no servidor, utilize a variável `SITE_PHP_LINT_BINARY`.

### Luz de fundo e pesquisa compacta

CodeMirror é habilitado por padrão em novas instalações. A configuração está localizada em “Bibliotecas e Fontes”. Na pesquisa compacta, o campo se expande para a esquerda, a cruz limpa a linha e a tecla Enter inicia a pesquisa.

### Menu de administração e painel de edição

O menu sob o cabeçalho do painel de administração e o painel de edição do site mostram ícones com legendas. Os ícones permanecem na tela estreita. As dicas de ferramentas são abertas ao passar o mouse e o teclado foca em uma tela estreita. “Ocultar” está localizado próximo ao restante das ações da página. O botão Edit Mode está localizado à direita.

### Descrição e código do módulo PHP

Em "Módulos PHP" a breve descrição é para texto simples. Cole o corpo da função em um campo separado “Código fonte · PHP”.

## Novo na versão 1.6.2

### Idioma de administração e ajuda

Nas configurações regionais, habilite os idiomas desejados. O menu de seleção de idioma no cabeçalho altera a interface e a ajuda, mantendo a seção atual. Os textos das páginas e os valores das suas configurações são armazenados em sua forma original.

### Decoração de porão

O código [author] exibe a assinatura Eurowebcart CMS Light, o nome do autor Бандалетов Павел e um link para GitHub. Para escolher onde colocar sua assinatura no rodapé HTML personalizado, cole [author] na linha apropriada.

### Luz de fundo no modo de edição

Passe o mouse sobre o link Editar componente ou defina o foco do teclado para ele. O quadro destacará a saída desta inserção específica.

### Onde alterar a pesquisa

Em “Configurações → Geral” selecione o tipo de pesquisa. Em “Manutenção” você pode reindexar páginas, e o campo no cabeçalho do administrador pesquisa por configurações.

### Resultados e sugestões

A pesquisa mostra 20 resultados numerados por página. O campo compacto se expande à esquerda da lupa e contém um ícone Enter. As sugestões da administração levam diretamente às configurações.

## Novidades da versão 1.6.1

### Pesquisa

Em Geral, escolha desativada, lupa ou campo expandido. O cabeçalho da administração pesquisa configurações. O índice é atualizado após alterações; use o agendamento para FTP. Páginas incluídas e blocos de dados entram no índice sem executar módulos PHP.

### Módulos PHP

Expanda um módulo para ver configurações e páginas que o utilizam. Crie um módulo com identificador, nome de arquivo e corpo da função PHP. Renomear o arquivo mantém [php]id[/php]. Ative CodeMirror para destacar a sintaxe.

### Modelos e traduções

Desfaça até oito alterações por modelo ou restaure o PHP original. Cada domínio tem histórico separado. Use HTML personalizado no painel e templates/local/ para PHP. O seletor de idioma aparece ao criar uma tradução e as traduções acompanham a página movida.

### Integrações e manutenção

CAPTCHA, mapas, telefonia e métricas contêm links oficiais. Informe URL e token HTTP do retorno de chamada. As configurações do subdomínio cadastrado permanecem ao passar para o domínio principal. Agende php update.php --scheduled a cada 15 minutos.

## Novidades da versão 1.6.0

### Pesquisa e cache

Reconstrua o índice de texto em Manutenção após publicar ou editar páginas pelo painel ou via FTP. As sugestões usam páginas públicas no idioma do visitante. O cache vem desativado e pode ser ligado ou limpo ali. Formulários e componentes dinâmicos são processados a cada solicitação.

### Edição no site

Após o login, o controle à direita da barra ativa os links de edição de componentes em todo o site. CSS e JS da página abrem em uma janela modal. No painel, use HTML, edição visual, inserção de códigos e envio em lote e ordenação de arquivos por AJAX.

### Componentes e navegação

Parâmetros ausentes usam a configuração global. width aceita 100, 50, 33 ou 25, com ou sem %. Em celulares os blocos ficam em uma coluna. numbers=off oculta o contador. Deslize para mudar de foto. O menu móvel abre pela direita e as trilhas de navegação podem ser desativadas nos ajustes gerais.

```text
[gal width=50 numbers=off arrows=on fit=cover]/images/myfotos/[/gal]
[fotos width=50% columns=3 captions=on]/images/myfotos/[/fotos]
[files width=33 icons=off size=on download=on]/files/myfiles/[/files]
```

### Idiomas, aparência e preços

Nas opções regionais, escolha idiomas, moeda, formatos e fuso horário. Coloque index_en.php ao lado de index.php para adicionar uma tradução; sem o arquivo, o conteúdo original aparece. O campo Preço usa a moeda escolhida. Veja as paletas, altere o modo em tempo real, envie os logotipos claro e escuro e teste Roboto Condensed com uma frase própria.

```text
/contacts/index.php → /contacts/index_en.php
```

### Formulários, solicitações e integrações

Defina chaves do Google reCAPTCHA v2 globais ou por formulário. Mensagens e retornos têm 20 itens por página, com os não lidos no topo. Integrações inclui Zadarma, Telegram, métricas e mapas Google ou Yandex com agrupamento opcional. A janela das bibliotecas testa a versão ativa e traz o código. CodeMirror tem Auto, HTML, CSS, JS, PHP e RAW.

```text
[map lat=55.7558 lng=37.6173 title="Office" zoom=13 provider=yandex url="/contacts/" button="Open"]
```

```text
[callback button="Call me" title="Callback" submit="Send" placeholder="+7(900)123-45-67"]
```

### Módulos PHP

Em Módulos PHP, defina o nome, a descrição e a disponibilidade. server-date usa a data em formato regional. Os desenvolvedores instalam os arquivos executáveis em uma pasta protegida.

```text
[php]server-date[/php]
```
