# Perguntas frequentes sobre administração

## Instalação e login

### Como lançar um site e abrir o painel de administração?

Para iniciar localmente, execute o comando php -S 127.0.0.1:8000 router.php no diretório do projeto e abra http://127.0.0.1:8000/admin/.. Na hospedagem, primeiro execute php admin/setup.php, depois insira o código único recebido na primeira etapa do assistente em /admin/.

### O que você pode configurar ao iniciar?

Primeiro crie um nome de usuário e uma senha. Na segunda etapa, defina o nome do site, meta descrição padrão, texto do rodapé, paleta e modo de tema. Se você possui PHP GD, pode baixar imediatamente o logotipo e criar um favicon a partir dele. Você pode pular a segunda etapa ou continuar na próxima vez que fizer login.

## Páginas e menus

### Como criar uma página e qual endereço inserir?

Vá para Páginas → Nova página. Insira o endereço, por exemplo /Services-1/Repair/ ou /Services-1/Price_2.php, preencha o título e salve. Para o endereço da pasta, o próprio mecanismo criará os diretórios e index.php. A-Z, a-z, 0-9, hífen e sublinhado são permitidos em nomes. A página principal é reservada na raiz e a criação de páginas nos diretórios de serviço é encerrada.

### Qual é a diferença entre a caixa de seleção “No Menu” e o botão “Ocultar”?

Desmarcar a caixa “No menu” remove o link dos menus normais, mantendo o acesso ao endereço direto. Em uma página com endereço de pasta, a caixa de seleção também controla itens em subpáginas. O botão “Ocultar” remove o link e fecha o endereço público da página até que ela seja publicada novamente.

### Como posso alterar a ordem das páginas ou mover uma pasta inteira?

Arraste o ícone ⠿ para reorganizar as páginas no mesmo nível. Para mover uma página, abra “Mover Página” em seu editor. Para transferir uma pasta junto com suas páginas e arquivos anexados, clique em ↪ ao lado do nome da pasta. Após a transferência, verifique os links manuais e os códigos [include] que apontavam para os endereços antigos.

### Por que as alterações de página não são imediatamente visíveis?

Clique em "Salvar" no editor. Em seguida, use o link "Abrir página" ou "Ir". Se o link desapareceu, verifique se a página está publicada. Para alterações em um subdomínio, verifique o domínio na barra de endereço.

## Design e subdomínios

### Onde posso alterar o título, rodapé, logotipo e favicon?

Abra Configurações → Geral. O campo Texto do rodapé suporta [year] para o ano atual. No bloco “Logos e ícones de sites”, você pode fazer upload de imagens ou clicar em “Criar um favicon a partir de um logotipo”. O carregamento e a conversão requerem PHP GD.

### Como mudar o tema ou fonte e depois voltar ao antigo?

Em Configurações → Tema e Cores, selecione um modo e uma paleta. Salve a paleta atual na lista para poder usá-la mais tarde. O botão "Usar como padrão" lembra a paleta do comando "Retornar tema ao padrão". A fonte pode ser alterada na seção "Bibliotecas e Fontes". Os nomes e exemplos na lista são mostrados nas fontes apropriadas. O valor "Sistema" retorna a fonte normal.

### Onde alterar o cabeçalho e onde inserir um contador, CSS ou JavaScript?

O cabeçalho dos blocos comuns, menu móvel, menu e rodapé são editados na aba “Modelos de Design”. No mesmo local, “Service Headers” insere HTML no cabeçalho ou antes da tag de fechamento do corpo. Para CSS e JavaScript regulares, há uma guia “CSS e JS” separada. O código de uma página é adicionado ao seu editor.

### Como definir diferentes números de telefone e cabeçalhos para subdomínios?

Adicione um subdomínio na seção “Domínios”. Em seguida, defina um valor de submodelo separado na seção Submodelos selecionando o domínio desejado. Altere o cabeçalho e outros blocos usando o botão “Modelos” próximo ao domínio. A versão do domínio tem precedência sobre a versão geral. O DNS e o servidor web devem apontar o subdomínio para o diretório desse site.

### Onde posso alterar os links das redes sociais e as legendas próximas a elas?

Abra “Configurações → Contatos e redes sociais”. Preencha endereços, assinaturas e números de telefone. O código [soc-icons] exibe ícones de todas as redes configuradas. Para um link único, utilize por exemplo [tg] ou [vk].

## Formulários e aplicações

### Como adicionar um formulário a uma página?

Abra Formulários, configure campos, comprimento máximo, obrigatório e texto dos botões. Salve o formulário e cole seu código, por exemplo [form1], no HTML da página.

### Onde posso visualizar as inscrições se não tiver recebido uma notificação por e-mail?

Abra Mensagens. As inscrições são salvas lá após o envio bem-sucedido do formulário. Para notificações, verifique os endereços no campo “Para” do modelo de formulário de email e a funcionalidade da função mail() na hospedagem.

### Como montar uma carta e enviar cópias?

Abra o formulário desejado e encontre o bloco “Mail Template”. Insira os endereços nos campos “Para” e “Cópias (Cc)”, separados por vírgulas. No assunto e no corpo, use chaves de campo entre chaves, por exemplo Olá {name}! Os botões abaixo do texto inserem a variável selecionada. O assunto ou texto em branco usa a notificação padrão.

### Por que o formulário rejeitou a mensagem ou perguntou “Não sou um robô”?

Verifique os campos obrigatórios, comprimento, telefone e formato de e-mail. Os campos de entrada gratuitos usam texto simples, sem HTML ou endereços de sites. As listas de palavras e IPs proibidos estão localizadas na seção Antispam. A caixa de seleção “Usar CAPTCHA” requer uma chave de site e uma chave secreta Google reCAPTCHA v2 para o domínio desejado.

### Onde posso ver as solicitações de retorno de chamada?

Na seção "Retornos de chamada". O componente é adicionado à página com o código [callback] ou [callback=Вызвать мастера]. Com o provedor “Não”, os aplicativos são salvos no painel de administração. Quando a telefonia está configurada, a transmissão manual ou automática está disponível.

## Blocos e componentes de informação

### Como criar um bloco de informações e exibi-lo na página?

Na seção “Infoblocks”, clique em “Novo Infoblock”, configure as colunas e adicione linhas. No nome do infobloco e nas chaves de coluna, use A–Z, a–z, 0–9, hífen ou sublinhado sem espaços. Salve e copie o código incorporado do editor de infoblock. Para obter uma lista de telefones, use o modelo de telefones.

### Como copiar ou substituir todas as linhas de um infoblock?

Abra o bloco de informações e clique em “Editar como texto”. CSV usa o | delimitador. As três primeiras linhas contêm as chaves, nomes e tipos das colunas. Em seguida vêm os dados. Coloque um campo com um delimitador, aspas ou quebra de linha entre aspas duplas e aspas internas duplas.

### O que verificar se a galeria ou lista de arquivos está vazia?

Verifique o caminho no código do componente com a pasta no servidor. Por exemplo, [fotos]/images/myfotos/[/fotos] usa fotos em images/myfotos/ ou templates/images/myfotos/.. Para [files], verifique a extensão do arquivo. Uma lista de extensões e parâmetros suportados pode ser encontrada na ajuda do componente.

### Por que uma solicitação com ?ajax=1 retorna 403?

O fragmento de página requer uma sessão pública válida e um token no mesmo site. Para carregar dentro do site, utilize window.siteFetchContent(), que transfere o token. Para uma transição normal, abra o endereço da página sem o parâmetro ajax.

## Atualizações e backup

### Como atualizar o motor de um site instalado?

Em Configurações → Atualizações, compare a versão instalada com a versão mais recente do GitHub e clique em Mecanismo de atualização. Se o GitHub não estiver disponível, o painel de administração mostrará a última versão verificada. Para atualizar manualmente, passe o arquivo php-site-update.zip para o comando php update.php. O pacote de atualização substitui os arquivos do mecanismo e os modelos padrão. Armazene seus modelos PHP em templates/local/.

### O que devo copiar antes de atualizar ou migrar?

Faça uma cópia de todo o diretório do site junto com os arquivos ocultos. As páginas estão localizadas na raiz e seus diretórios, blocos de informações em data/, configurações e arquivos de conta em admin/.. Salve também seus modelos, imagens enviadas, mensagens e solicitações. A cópia inclui segredos de configuração, portanto mantenha-a fora de um diretório público.

### O que verificar se o painel de administração não consegue salvar as alterações?

Primeiro leia a mensagem ao lado do formulário. Se as alterações entrarem em conflito, copie suas edições e abra o editor novamente. Caso haja erro de gravação, peça à hospedagem para verificar as permissões de gravação do processo PHP na pasta e arquivo desejado admin/write-lock.php.. Caso a atualização falhe, verifique o acesso do servidor ao GitHub e a disponibilidade de PHP Zip e OpenSSL.

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

## Botões no cabeçalho da administração · 1.6.4

O globo abre o site e a seta termina a sessão. No telemóvel, só aparecem os ícones. Passe o cursor ou utilize o teclado para ver os respetivos rótulos.

Conteúdo, Formulários e pedidos, e Site e serviços abrem diretamente. Os separadores da secção escolhida aparecem abaixo do menu principal.

Insira [bread-crumbs] ou [search] no HTML da página inicial (index.php) ou de um modelo. Cada bloco aparece uma vez na posição escolhida. As definições do caminho de navegação e da pesquisa continuam ativas. [privacy] liga a /privacy/. Edite a política em Páginas.

```text
[bread-crumbs]
[search]
[privacy]
```

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

## Editar dados na própria página · 1.6.6

No modo de edição, os blocos, formulários [formX], fragmentos [tel] e outros componentes com editor oferecem Dados e Ecrã inteiro. A lista de vistas contém os modelos registados. Escolha Pasta raiz para mover a secção para o nível superior. Use [list-pages] para listar as páginas publicadas na secção e nas subpastas. Feche o menu de idiomas clicando fora ou premindo Escape.

## Menu móvel · 1.6.7

- Mostrar no menu principal
- Mostrar no menu móvel
- Ocultar no menu móvel

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
