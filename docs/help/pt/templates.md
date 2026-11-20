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
