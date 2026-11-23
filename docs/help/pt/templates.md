# Modelos e componentes

## Modelos de apresentação

### Cabeçalho, menu e rodapé

A página completa conecta esses blocos automaticamente. As versões gerais são editadas em “Modelos de Design” e para um domínio individual - na seção “Domínios”.

```text
[header]
[mobile-menu]
[menu]
[footer]
```

### Inserindo outra página

Insere o conteúdo de uma página publicada. O aninhamento máximo é de dez níveis. A repetição de uma página na mesma cadeia é ignorada.

```text
[include]/contacts/[/include]
```

### Links no cardápio

A caixa de seleção “Mostrar no menu” adiciona um link para os menus padrão. Se o nome estiver vazio, o título será usado. Se você desmarcar a caixa de seleção de uma página de pasta, suas subpáginas também desaparecerão do menu.

```text
MenuVisível: 1
Cardápio: Contatos
```

### CSS e JavaScript

O código geral é editado na aba “CSS e JS”. O código da página é editado no bloco “páginas CSS e JavaScript” e incluído após o bloco geral. Cole o conteúdo CSS e JavaScript nos campos.

```text
.page-content { max-width: 72rem; }
```

```text
document.documentElement.classList.add('custom-script-loaded');
```

### Cabeçalhos de serviço

Na seção “Modelos de design → Cabeçalhos de serviço”, você pode inserir meta tags gerais e códigos de contador dentro do cabeçalho ou antes do corpo de fechamento. Canonical é adicionado automaticamente em páginas inteiras.

```text
<meta name="theme-color" content="#065f63">
```

### Ícones impressionantes de fontes

Font Awesome Free 7.3.1 está conectado. O código curto funciona em blocos de conteúdo e design HTML. Estão disponíveis estilos sólidos, regulares e de marcas. Adicione texto próximo ao ícone se ele representar uma ação.

```text
[fa-icon=house] [fa-icon=regular:envelope] [fa-icon=brands:github]
```

```text
<i class="fa-icon fa-solid fa-phone" aria-hidden="true"></i> Contato
```

### Bibliotecas e fontes

As conexões são gerenciadas em “Configurações → Bibliotecas e fontes”. Para Material UI, use JavaScript modular. A fonte do Google selecionada substitui a fonte do sistema do site.

```text
<script type="module">import { Button } from "@mui/material";</script>
```

```text
GLightbox({selector: ".glightbox"});
```

## Variáveis, submodelos e data

### Variáveis ​​do site

Adicione entradas em “Configurações → Geral”. Insira a chave no texto, cabeçalho, título do site ou rodapé.

```text
[city] = Moscou
[spb] = São Petersburgo
```

```text
Atendimento na cidade [city]
```

### Fragmentos

Na seção Submodelos, especifique um nome, uma descrição e um valor. Para um domínio, seu valor é obtido primeiro e depois seu valor geral. O submodelo pode conter HTML. A marcação nos cabeçalhos é removida.

```text
[tel]
```

```text
[title]
```

### Data e hora

O fuso horário é retirado do PHP. [year] - ano, [month] - mês, [day] - dia do mês, [time] - horas e minutos, [dmY] e [date] - data DD.MM.AAAA, [datetime] - data e hora.

```text
© [year] [city]
Data [dmY], hora [time]
```

### Nome e autor do CMS

O código [author] gera Eurowebcart CMS Light, o nome do autor Бандалетов Павел e um link para GitHub. Num rodapé padrão, a assinatura aparece automaticamente. No seu próprio rodapé HTML, cole o código onde desejar.

```text
[author]
```

### Modelo e política inicial

[page-content] insere o conteúdo da página inicial em [home]. Abra /privacy/ em Páginas para editar a política. [site-name] e [site-url] vêm das definições gerais.

```text
[page-content]
```

```text
[site-name] · [site-url]
```

### Links e configurações no rodapé

[privacy] liga a /privacy/, [top] regressa ao topo e [cookies] abre as definições de consentimento.

```text
[privacy]
[cookies]
[top]
```

### Funções PHP comuns

Na seção “Modelos de Design → Funções”, edite functions.php. No código PHP, translit() ou ew_call() chama uma função registrada. O código curto [func=translit] está disponível no conteúdo e no campo “HTML personalizado”.

```text
[func=translit value="Москва и München"]
```

```text
$var = translit("Moscou");
```

```text
echo ew_escape(ew_call("translit", "München"));
```

## Carrossel, galeria de fotos e arquivos

### Carrossel [gal]

Mostra fotos de uma pasta. setas e números podem ser ativados ou desativados, efeito – deslizar, desbotar ou nenhum, ajustar – conter ou cobrir.

```text
[gal]/images/myfotos/[/gal]
```

```text
[gal arrows=on numbers=off effect=fade fit=cover width=50]/images/myfotos/[/gal]
```

### Galeria de fotos [fotos]

Mostra uma grade responsiva. colunas — automático ou um número de 1 a 6, proporção — 4x3, 1x1, 16x9 ou original, ajuste — cobrir ou conter. legendas e links são ativados ou desativados.

```text
[fotos]/images/myfotos/[/fotos]
```

```text
[fotos columns=4 ratio=1x1 fit=cover captions=on links=off width=50%]/images/myfotos/[/fotos]
```

### Lista de arquivos [files]

Lista os arquivos suportados. ícones, tamanho e download aceitos ativados ou desativados, ordem – asc ou desc. Zip, pdf, xls, xlsx, doc, docx, png, gif, jpg, jpeg, mp4, mov e mp3 são permitidos.

```text
[files]/files/myfiles/[/files]
```

```text
[files icons=on size=on order=desc download=on width=33]/files/myfiles/[/files]
```

## Formulários e retorno de chamada

### Formulário [formN]

Na seção “Formulários”, configure os campos, seu comprimento máximo, texto do botão e destinatário. “Usar CAPTCHA” habilita o widget Google reCAPTCHA v2 com verificação de resposta no servidor. As solicitações são enviadas via AJAX e ficam visíveis na seção Mensagens.

```text
[form1]
[form2]
```

### Modelo de formulário de correio

O assunto e o texto são armazenados separadamente para cada formulário. Em seu modelo de e-mail, insira chaves de campo entre chaves. As listas de destinatários e cópias são separadas por vírgulas.

```text
Olá {name}!
Telefone: {phone}
Mensagem: {message}
```

### Ligue de volta [callback]

Abre um formulário com um número de telefone. O aplicativo é salvo na seção “Callbacks”. O rótulo do botão pode ser definido em código. Com o provedor “Não”, o envio para telefonia fica desabilitado.

```text
[callback]
[callback=Вызвать мастера]
```

## Blocos de dados

### Tabela e lista de telefones

Crie um conjunto de dados na seção "Infoblocks". A–Z, a–z, 0–9, hífen e sublinhado são permitidos no nome do infobloco e nas chaves de coluna. Não são permitidos espaços. O modelo de tabela exibe uma tabela, telefones exibe colunas de telefone com links para chamadas. Os códigos de fechamento [iblock] e [/iblock] são suportados.

```text
[iblock=table]/data/myiblock.txt[iblock]
[iblock=phones]/data/myiblock.txt[/iblock]
```

## Contactos e redes sociais

### Bloco geral e ícones

[contacts] exibe um bloco de contatos, [phones] mostra números de telefone, [soc-icons] coleta ícones de todas as redes sociais com links customizados.

```text
[contacts]
[phones]
[soc-icons]
```

### Redes sociais individuais

Cada código exibe um link para sua rede social. Endereços e assinaturas são definidos em “Configurações → Contatos e redes sociais”.

```text
[tg] [vk] [whatsapp] [youtube] [instagram] [max]
```

```text
[wa] [yt] [ig]
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

## Ocultar a árvore de páginas · 1.6.15

No computador, a seta recolhe a árvore de páginas e amplia o editor. O botão acima do editor restaura a árvore. O navegador guarda a escolha.

## Escolher um ponto no mapa

Crie uma chave para «JavaScript API» no console do Yandex Maps e restrinja-a por referência HTTP. Na aba Mapas da página, escolha um ponto e aplique as coordenadas. Para testes locais, indique localhost sem porta como referência HTTP, aguarde a ativação e abra a administração usando localhost.

## Pesquisa no menu · 1.6.16

[search] apresenta a pesquisa junto às ligações de [main-menu] no computador e dentro de [mobile-menu] no telemóvel. Selecione o modo nas configurações gerais. [search] no conteúdo tem prioridade.

`[search]`

## Pesquisa no menu · 1.6.17

[search] apresenta a pesquisa junto às ligações de [main-menu] no computador e dentro de [mobile-menu] no telemóvel. Selecione o modo nas configurações gerais. [search] no conteúdo tem prioridade. O campo de pesquisa aparece ao abrir o menu móvel. [lang] mostra a seleção de idioma nesse menu e no cabeçalho no computador.

`[search]` · `[lang]`

O fundo do menu móvel mostra os contactos e as redes sociais configurados.

## Subtítulo do site

O subtítulo das configurações gerais aparece em tamanho menor abaixo do nome no cabeçalho. No celular, o nome também é menor.

## Cidades

Em Cidades, associe um domínio registado e configure até cinco números. O campo de telefone aceita códigos como [tel]. [cities] mostra o seletor, [phones] lista os números configurados e [tel], [tel2]–[tel5] mostram os números individuais. O navegador memoriza a escolha e atualiza os números sem recarregar a página.

`[cities]` · `[phones]` · `[tel]`

## Animação ao deslocar

Nas definições gerais, escolha o efeito de entrada dos blocos ao deslocar a página. Está desativado por predefinição e respeita a opção de movimento reduzido do dispositivo.

## Modelos de página

Escolha um modelo ao criar uma página. O conteúdo e os campos SEO são preenchidos e podem ser editados. Adicione os seus modelos em Modelos de página. Mapas, formulários e compras utilizam as definições de cada componente.

O CSS e o JavaScript do modelo são copiados para os campos da página. Adicione classes próprias em HTML e defina-as no CSS do modelo.

### Classes reutilizáveis

Contentor, grelha adaptável, secção em destaque, cartão e preço realçado.

`.page-preset` · `.page-preset-grid` · `.page-preset-hero` · `.page-preset-card` · `.page-preset-price`

Os modelos de layout começam recolhidos. Abra um modelo pelo título ou escolha Expandir tudo. Prima novamente para recolher a lista.

Para alterar a largura do carrossel, ative o modo de edição, abra Editar componente junto dos banners e selecione a largura do bloco.

## Cidades · Telefone · 1.6.18

Só o nome da cidade é obrigatório. O domínio e os telefones são opcionais. Use + e o indicativo do país, por exemplo +7 (900) 000-00-00. Pode usar um código como [tel] como alternativa.

É usada a cidade memorizada, depois a cidade do domínio e por fim a primeira da lista. Sem cidades, são apresentados os telefones gerais.

Os formulários, pedidos de chamada e novas encomendas usam o e-mail da cidade ou o do site se estiver vazio. Se ambos estiverem vazios, os formulários mantêm os seus destinatários. Os links sociais vazios são herdados.

No modo de edição, passe o cursor sobre um modelo para o editar. Para qualquer componente [php] instalado, Editar módulo abre o código e as definições numa janela. O editor de diapositivos mostra o nome e o código do formulário. [banners width=100] ocupa toda a janela como bloco autónomo sem barra lateral. O editor de modelos abre numa janela modal do site.

O gestor verifica referências antes de eliminar e faz uma cópia de segurança de pastas não vazias. Cada banner pode ter título e subtítulo.

O telefone é verificado durante a introdução. Nos números russos, o 8 inicial passa a +7. Após o envio, o formulário é ocultado e a confirmação surge ao reabrir a janela.

O indicativo escolhido é acrescentado a números sem +. Os números com + mantêm o indicativo introduzido.

## Temas de design

Copie o ficheiro para o tema e guarde. Ficheiros em falta usam a versão fornecida. Alterações, novos códigos, histórico e definições de domínio ficam no tema ativo. Use css/theme.css e js/theme.js para estilos e scripts adicionais.

`templates/themes/demo/header.php` · `templates/themes/demo/components/banners.php`

Os idiomas ficam no fundo do menu móvel. No computador, o globo fica junto à pesquisa no menu abaixo do cabeçalho.

Galerias, banners e listas procuram primeiro no tema ativo e, se faltar a pasta, nos ficheiros comuns. Pastas novas, carregamentos, logótipos e favicons ficam no tema. A cópia de segurança inclui a pasta e as definições do tema.

Escolha a largura da janela (width=page), do conteúdo (width=content) ou do bloco principal (width=parent). height define a altura no computador e mobile-height no telemóvel. auto mantém o tamanho natural.

`[banners width=page height=520 mobile-height=240]` · `[form1 width=parent]`

O último item do percurso mostra as secções publicadas do mesmo nível, incluindo as ocultas no menu principal. Os links usam o URL configurado.

## Temas de design · КОНТУР · 1.6.19

Um tema de site completo muda o design, as páginas, os blocos de dados e as configurações. Os demais temas usam as páginas compartilhadas.

Uma marca indica que o ficheiro do tema tem prioridade sobre o ficheiro padrão do CMS. Pode editá-lo ou restaurá-lo.

**Restaurar a versão de fábrica**

### Portfólio

Abra kontur-works em Conteúdo → Blocos de dados. Indique título, categoria, descrição, fotos separadas por vírgulas e um link opcional. A primeira foto será a capa. Use as mesmas colunas para outras coleções.

```text
[portfolio]
[iblock=portfolio limit=2]/data/kontur-works.txt[/iblock]
[maps source=kontur-map]
```

### Cores do site · Fontes

Marque as fontes a ativar e atribua-as ao texto, aos títulos ou aos controlos. O seu site aloja os ficheiros locais.

Bibliotecas → Hospedar localmente

Modelos de página → Criar página

## Anúncio

O anúncio aparece nas listas de páginas e nos resultados da pesquisa. Nas listas, a imagem tem prioridade sobre o ícone. Nos menus, o ícone aparece antes do nome da página.

No editor, clique em «Inserir lista…». Escolha a secção atual para `[list-pages]` ou clique em «Inserir» junto de uma pasta. `[dir]/sample/[/dir]` lista as páginas publicadas em /sample/ e nas suas subsecções. `[dir]/[/dir]` começa na raiz.


Introduza um nome no seletor de ícones. Por exemplo, home também encontra ícones com house no nome.

Abra Componentes da página → [banners] → Slides do banner para adicionar, editar ou reordenar os slides do conjunto. Editar componente abre as opções do bloco.

Os campos da tabela são alinhados no topo. Arraste o canto de um campo multilinha para alterar a largura e a altura.

Arraste o limite direito do cabeçalho ou use as setas para a esquerda e para a direita para redimensionar a coluna.

## Mapa das páginas da secção · `[maplist]`

Selecione «Mapa das páginas da secção» ou insira `[maplist]`. O mapa mostra as páginas publicadas com coordenadas da secção e das suas subsecções. Na página inicial abrange todo o site. Cada janela mostra o título, o ícone, a imagem em toda a largura, o anúncio e um link. Configure o mapa em Integrações.

`[maplist]/sample/[/maplist]` mostra o mapa da secção escolhida. `[points]/sample/[/points]` lista as páginas com coordenadas em cartões com título, imagem, anúncio e botão para abrir a página. O botão «Abrir trajeto» usa as coordenadas do ponto e o fornecedor escolhido nas integrações.

A opção de inclusão em sitemap.xml está no separador SEO. Nas definições da página, ative «Expandir o ramo do menu». As subpáginas visíveis abrem ao passar o cursor ou ao tocar na seta no telemóvel.

Insira `[route]` numa página com coordenadas. O link usa o fornecedor de mapas escolhido nas integrações. Use provider=yandex ou provider=google para o escolher explicitamente. Se os mapas estiverem desativados, é usado o Yandex.

## Campos personalizados

Use 1 a 64 letras latinas, algarismos, hífens ou sublinhados. Preencha a nova linha e guarde as definições.

Os campos são comuns e os valores são guardados por página. Insira [u-ID] ou selecione o campo no editor.

## Modelo da lista de pontos

Selecione PHP personalizado. Os pontos estão em $context['pointItems'] e os campos em $point['customFields'].
