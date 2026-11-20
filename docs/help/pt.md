# Português · Eurowebcart CMS Light

- [Gerenciamento](pt/readme.md)
- [Perguntas frequentes sobre administração](pt/faq.md)
- [Modelos e componentes](pt/templates.md)

## Menu móvel · 1.6.7

- Mostrar no menu principal
- Mostrar no menu móvel
- Ocultar no menu móvel


## Editar dados na própria página · 1.6.6

No modo de edição, os blocos, formulários [formX], fragmentos [tel] e outros componentes com editor oferecem Dados e Ecrã inteiro. A lista de vistas contém os modelos registados. Escolha Pasta raiz para mover a secção para o nível superior. Use [list-pages] para listar as páginas publicadas na secção e nas subpastas. Feche o menu de idiomas clicando fora ou premindo Escape.


## Ficheiros de configuração, mapas e modelos · 1.6.5

Abra Manutenção → Ficheiros de configuração para editar .htaccess, robots.txt e sitemap.xml. O gerador usa as páginas publicadas assinaladas para inclusão. A versão anterior de .htaccess pode ser recuperada.

Indique a latitude e a longitude. [map] mostra o ponto da página e [maps] reúne os pontos das páginas publicadas. center e zoom definem o enquadramento. O fornecedor de mapas é escolhido em Integrações.

[main-menu] é o menu sob o cabeçalho, com secções expansíveis. Crie modelos próprios e configure a visibilidade por dispositivo. Nos fragmentos, o botão de adicionar domínio abre o formulário. Use [privacy] para ligar à política.

Em Integrações, introduza os ID do JivoSite, Microsoft Clarity e Umami Cloud, o código Botfaqtor ou o webhook do Slack. As instruções estão junto aos campos. No Zadarma, consulte o saldo ou teste SMS e chamadas. Os testes podem ter custos. Para [payment], indique os links YooMoney, PayPal e SBP. Confirme o estado no painel do prestador.

Os editores HTML e visual têm ícones para formatação, títulos H1–H5 e listas. Coloque o cursor e selecione um modelo, componente, imagem ou ícone. Pode escolher formulários e opções dos componentes. Font Awesome fica disponível com a biblioteca ativa.

Clique no nome da pasta para gerir a secção à direita. Escolha a secção superior, configure os menus ou indique um URL de redirecionamento. Uma secção vazia lista as subsecções. Em Manutenção, acrescente regras como /contact.html => /contacts/ e edite as páginas em /error/. O idioma e a pesquisa têm ícones no telemóvel.

```text
[map]
[map center="55.7558,37.6173" zoom=14]
[maps center="55.7558,37.6173" zoom=11]
[main-menu]
[lang]
[privacy]
[payment]
```

```php
$var = translit("Москва"); // Moskva
```


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

## Configurações da página · 1.6.8

Escreva o conteúdo em Edição. Configure endereço, menu e calendário de publicação nas configurações da página. SEO reúne títulos, descrição, palavras-chave e inclusão no sitemap. Em Mapas estão as coordenadas e os exemplos.

Deixe as datas em branco para retirar os limites. Vale o fuso horário do site. Uma página oculta deve ser publicada separadamente.

- `[map]` — mapa com o ponto da página atual.
- `[maps]` — mapa com os pontos de todas as páginas publicadas.

## Banners e editor de imagens · 1.6.8

Gerencie conjuntos e slides em Componentes. Use fotos de uma pasta, adicione texto e botões para links, formulários ou chamadas de retorno e programe a exibição. O modo de edição mostra as ações do slide. O seletor permite navegar pelas pastas, enviar, excluir e criar cópias giradas ou redimensionadas com GD. Pesquise ícones do Font Awesome pelo nome.

```text
[banners]
[banners set=summer arrows=on numbers=off]
[banners]/images/myfotos/[/banners]
```

## Idiomas · 1.6.9

A aba Idiomas lista os idiomas da página. Abrir leva à versão salva. Criar tradução abre um editor vazio.

Uma subseção fica dentro da seção atual, uma seção no mesmo nível fica ao lado e uma seção na raiz fica no nível superior do site.

## Compatibilidade com PHP e navegação · 1.6.10

A versão mínima do PHP é 7.2. O site e a administração suportam PHP 8.6. Use nos seus módulos e modelos a sintaxe da versão instalada no servidor.

O item atual de [bread-crumbs] abre as seções publicadas do mesmo nível que aparecem no menu principal. A URL configurada é usada em menus, listas de seções, trilhas de navegação, resultados e sugestões de pesquisa.

A lixeira remove uma linha do editor de infoblocos. Remover uma coluna apaga todos os seus valores. As alterações são gravadas ao salvar, inclusive automaticamente após a reordenação.

### Cópias de segurança

O ZIP privado contém páginas, mídias, blocos, configurações, modelos e módulos PHP. A conta e as solicitações ficam separadas. É necessário PHP Zip.

Substituir conteúdo e configurações. Antes disso, será feita uma cópia do estado atual.

### Limpar arquivos sem uso

A busca encontra mídias sem referências no conteúdo salvo. Revise a lista e selecione os arquivos. Antes é criada uma cópia de segurança. Remover o código de um componente preserva sua pasta e seus arquivos.

### Escolher um ponto no mapa

Crie uma chave para «JavaScript API» no console do Yandex Maps e restrinja-a por referência HTTP. Na aba Mapas da página, escolha um ponto e aplique as coordenadas.

Selecione texto para alinhar ou limpar a formatação. Clique na imagem para editá-la e arraste a imagem ou seu canto para mover ou redimensionar. Links aceitam URL, e-mail, telefone e âncora. Botões abrem URLs, formulários, retornos de chamada ou links de pagamento do fornecedor.

As abas HTML e Editor visual alternam o modo. SUP, SUB e Parágrafo inserem os elementos correspondentes. Os componentes mostram seus códigos. As pastas são criadas na inserção. Os nomes aceitam letras latinas, dígitos, hífens e sublinhados. A aba Idiomas leva às configurações de idioma.

```text
[button action="url" label="Contacts" url="/contacts/"]
[button action="form" label="Write to us" form="form1"]
[button action="callback" label="Call me"]
[button action="payment" label="Pay" amount="1250.00" currency="RUB" yoomoney="410010000000000"]
[map]
[maps]
```

## Venda · 1.6.11

Defina preço e pagamento na aba Venda, com valores opcionais por idioma. Insira [item-price] e [item-buy-button]. O pedido é registrado antes do pagamento. Os status podem ser enviados por email e SMS Zadarma.

Pedido recebido · Pagamento aceito · Enviado · Encerrado · Cancelado

```text
[item-price]
[item-buy-button]
```

Selecione um arquivo ou URL para inserir uma imagem. Clique na imagem para ajustar tamanho, alinhamento, quatro margens e CSS. O alinhamento da imagem é independente do texto. Em HTML, os blocos ficam em linhas separadas. [list-pages] está em Componentes. O logotipo usa as cores do tema.

## Gráfico do bloco de dados

No editor da página, abra a inserção de componentes e escolha uma tabela ou um gráfico. Para gráficos, ative o Chart.js, escolha a coluna de rótulos e colunas do tipo Número ou Preço. Pizza, rosca e área polar usam uma série não negativa. Com a biblioteca desativada, a tabela é exibida.

Formulários, modelos, trechos salvos e componentes registrados aparecem ao reabrir o editor. O ID de um novo conjunto aceita A–Z, a–z, hífens e sublinhados. Após enviar imagens para uma pasta [banners], aparece um link para editar o slide ao lado de cada arquivo. No modo de edição, a janela exibe o slide escolhido e oferece um link para as configurações padrão. Ao lado de Salvar, o gerenciador de carrosséis, galerias e listas permite enviar, excluir, ordenar e criar subpastas (A–Z, a–z, 0–9, - e _). As cores aparecem em quadrados.

```text
[iblock=table]/data/myiblock.txt[/iblock]
[iblock=charts type=line labels=month values=sales,orders]/data/myiblock.txt[/iblock]
[banners set=summer-promo]
```

## Cabeçalho e rodapé da administração · 1.6.12

No computador, Eurowebcart CMS e Light com a versão instalada aparecem junto do logotipo. No celular, o símbolo fica na mesma linha que os botões. O rodapé contém o endereço do repositório. Verifique atualizações cumulativas nas configurações.

## Cabeçalho e rodapé da administração · 1.6.13

No computador, Eurowebcart CMS e Light com a versão instalada aparecem junto do logotipo. No celular, o símbolo fica na mesma linha que os botões. O rodapé contém o endereço do repositório. Verifique atualizações cumulativas nas configurações. O nome padrão Página inicial na lista segue o idioma da administração. O logotipo do rodapé mostra Eurowebcart CMS e LIGHT em duas linhas.

## Informações do PHP

Em Manutenção, phpinfo() mostra a versão, os módulos e a configuração do PHP a administradores autenticados. As variáveis de ambiente e de requisição são ocultadas. Ajuste error_reporting nas configurações gerais. O padrão é o nível do servidor.

## Ocultar a árvore de páginas · 1.6.14

No computador, a seta recolhe a árvore de páginas e amplia o editor. O botão acima do editor restaura a árvore. O navegador guarda a escolha.
