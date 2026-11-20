# Eurowebcart CMS Light

Autor - Бандалетов Павел. [Projeto original e atualizações](https://github.com/bandaletov72/php-site-updates).

A versão mínima do PHP é 7.2. O site e a administração suportam PHP 8.6. Use nos seus módulos e modelos a sintaxe da versão instalada no servidor.

[Perguntas frequentes sobre administração](faq.md) também está disponível na aba “Ajuda”.

[Ajuda para a versão atual em dez idiomas](../README.md) descreve pesquisa, edição de sites, localização e manutenção. [Admin FAQ in English](../en/faq.md).

Para manutenção programada adicione o comando `php update.php --scheduled` ao agendador da hospedagem com período de 15 minutos. Na seção Manutenção, você pode habilitar a reindexação de forma independente e definir o intervalo de verificação para atualizações assinadas do GitHub.

## Lançamento local

Vá para o diretório `php-site` e inicie o servidor PHP integrado.

```sh
php -S 127.0.0.1:8000 router.php
```

O site será aberto no endereço `http://127.0.0.1:8000/`, o painel de administração está localizado em `http://127.0.0.1:8000/admin/`.

## Instalação e primeiro login

No seu servidor local, abra `/admin/` e crie uma conta. Na segunda etapa, defina o nome do site, meta descrição, texto do rodapé, paleta e modo de tema. O logotipo pode ser baixado imediatamente se você tiver PHP GD. O assistente criará um favicon a partir dele. Clique em “Salvar e abrir admin” ou continue com as configurações atuais. Se você sair do assistente, ele continuará na próxima vez que você fizer login.

Na hospedagem, execute `php admin/setup.php` no diretório do projeto e insira o código único na primeira etapa do assistente. O código é resgatado quando você cria uma conta. Executar o comando novamente antes da instalação substitui o código anterior. A instalação da atualização mantém a conta e as configurações anteriores. Caso a versão anterior à atualização utilizasse `SITE_ADMIN_PASSWORD_HASH`, o login é salvo com o login `admin` e a senha anterior.

Para que o aplicativo funcione, você precisa de sessões PHP e direitos de gravação em `admin/`, `admin/write-lock.php`, `admin/inbox/`, `data/` e diretórios de páginas editáveis. Para criar páginas e diretórios na raiz, conceda ao processo PHP acesso de gravação à raiz do projeto. Utilize um servidor web com HTTPS, processando `.php` e `index.php` na lista de arquivos de índice. Desative listagens de diretórios. Para HTTPS atrás de um proxy reverso, passe o atributo PHP `HTTPS` por meio da configuração do servidor confiável.

## Páginas e menus

No painel de administração, a página principal está listada como `/`, o restante das páginas nas pastas estão listadas como `/contacts/`. Para criar uma subpágina, insira, por exemplo, `/Services-1/Repair/`. O painel de administração criará os catálogos e o próprio `index.php`. Para um arquivo separado, insira um endereço como `/Services-1/About_2.php`. `A–Z`, `a–z`, `0–9`, hifens e sublinhados são permitidos em nomes de arquivos e pastas. Os nomes são inseridos sem espaços. Na raiz, apenas a página principal é permitida. Diretórios de serviços incluindo `data`, `admin`, `templates`, `css` e `js` são reservados.

Na lista de páginas, as pastas podem ser expandidas. Na caixa de seleção Menu, ocultar, publicar e excluir são salvos via AJAX. Ocultar, publicar e excluir requerem confirmação. Arraste o ícone ⠿ para classificar as páginas adjacentes. A página oculta para de abrir e desaparece do menu antes da publicação.

No editor de páginas, abra o bloco “Mover página” e especifique a pasta de destino. Por exemplo, se você transferir `/contacts/` para `/company/`, o novo endereço será `/company/contacts/`. As páginas aninhadas permanecem nos mesmos endereços. Atualize seus próprios links e códigos `[include]` que levam à página migrada.

Arraste a alça ⠿ para ordenar páginas no mesmo nível. Mova uma página em suas configurações. O ícone da pasta abre a seleção da seção superior. Escolha outra seção ou a raiz e clique em Mover pasta. As páginas e os arquivos vão junto. Confira links antigos e códigos [include].

O conteúdo pode ser editado em um editor de texto e adicionado via FTP ou SFTP. O arquivo deve estar em UTF-8 sem BOM. Salve o bloco de serviço PHP até a linha `__halt_compiler();`. No novo arquivo de paginação, insira este bloco do exemplo na profundidade desejada e verifique o caminho relativo à raiz `index.php`. Em seguida vêm os cabeçalhos, uma linha vazia e um fragmento HTML da página.

```php
<?php
$pageFile = __FILE__;
require __DIR__ . '/../index.php';
__halt_compiler();
Title: Контакты
Heading: Как с нами связаться
Description: Контактная информация
Menu: Контакты
MenuVisible: 1
Order: 20

<p>Укажите здесь контактную информацию.</p>
```

`Title` é obrigatório. `Heading` vazio é retirado de `Title`. `Description` preenche a meta descrição da página. `MenuVisible: 1` mostra o link no menu, `MenuVisible: 0` o oculta. Para a pasta `index.php`, desmarcar a caixa de seleção também oculta todas as subpáginas do menu. Caso falte `MenuVisible`, o checkbox herda a regra anterior e é considerado habilitado quando `Menu` for preenchido. Quando o checkbox estiver habilitado, o campo vazio `Menu` é substituído pelo cabeçalho `Title`. `Order` especifica a sequência dentro de um nível e o padrão é 100.

A guia CSS e JS armazena CSS e JavaScript comuns para páginas públicas. O editor de páginas possui um bloco separado “páginas CSS e JavaScript”. O código da página é carregado após o geral. Especifique apenas o conteúdo dos arquivos, sem as tags `<style>` e `<script>`. O tamanho permitido de cada campo é de até 64 KiB. Para vincular imagens de CSS, você pode usar caminhos da raiz do site. A guia Ajuda contém exemplos de modelos e componentes disponíveis.

Na seção “Modelos de Design” você pode visualizar as fontes de `header.php`, `footer.php`, `menu.php` e `mobile-menu.php` e definir HTML comum para cada uma. "Cabeçalhos de serviço" inserem meta tags comuns, códigos de contador e scripts em `<head>` ou antes de `</body>`. Em cada página completa publicada, `rel="canonical"` é formado a partir de seu endereço regular sem parâmetros de consulta.

## Configurações, submodelos e domínios

Na seção “Configurações” você define o nome do site, meta descrição padrão, texto do rodapé e variáveis. No texto do rodapé, `[year]` exibe automaticamente o ano atual. As variáveis ​​são escritas uma por linha, por exemplo `[city] = Москва` e `[spb] = Санкт-Петербург`. Para exibir um valor no título do site, conteúdo da página, cabeçalho ou rodapé, insira `[city]`. Nos modelos PHP você pode usar `site_escape($settings['variables']['city'] ?? '')`.

As substituições do sistema incluem `[year]`, `[month]`, `[day]`, `[time]` (horas e minutos), `[dmY]` e `[date]` (data DD.MM.AAAA), `[datetime]` (data e hora). O fuso horário PHP é usado.

Em “Básico → Logotipos e ícones de sites” você pode fazer upload de PNG, JPEG, GIF ou WebP. Os downloads são feitos pelo PHP GD. O botão criar favicon cria um PNG transparente de 64×64 a partir do logotipo selecionado ou salvo. As imagens são armazenadas em `templates/images/branding/` e `templates/icons/branding/`.

"Subtemplates" armazena o nome, a descrição e o significado de cada código. Para o telefone, crie `[tel]`. Para definir um título de guia por meio de um submodelo, crie `[title]` e especifique-o no campo `Title` da página. Após o título da aba, o nome do site é exibido. As substituições funcionam no texto da página e nos metadados de texto. Para o link, insira toda a tag `<a>` como valor de submodelo e coloque o código no texto entre as tags. Nos metadados, o HTML é convertido de valor em texto.

Adicione subdomínios na seção “Domínios”. No painel de administração do subdomínio de mesmo nome, selecione “Domínio atual” ou abra o domínio desejado na lista geral. O significado local tem precedência sobre o significado geral. A página pública seleciona valores por domínio em seu endereço, e os links internos o salvam. Para conectar um subdomínio, configure o DNS e o servidor web em sua hospedagem para que o domínio sirva o mesmo diretório do projeto.

O botão “Modelos” próximo ao domínio abre o editor `[header]`, `[mobile-menu]`, `[menu]` e `[footer]`. Para cada bloco você pode definir seu próprio HTML ou usar a versão geral. Os modelos gerais são configurados na mesma seção.

## Componentes em HTML

| Código | Resultado |
| --- | --- |
| `[gal]/images/myfotos/[/gal]` | Carrossel de fotos com rolagem suave, setas e deslizamento |
| `[fotos]/images/myfotos/[/fotos]` | Galeria de fotos com links para originais |
| `[files]/files/myfiles[/files]` | Lista de arquivos com ícones por extensão |
| `[include]/contacts/[/include]` | HTML de outra página com seus submodelos e componentes |
| `[form1]` | Formulário configurado na seção Formulários |
| `[callback=Вызвать мастера]` | Botão e formulário de solicitação de retorno de chamada |
| `[iblock=table]/data/myiblock.txt[iblock]` | Tabela de bloco de informações |
| `[iblock=phones]/data/myiblock.txt[iblock]` | Lista de telefones do bloco de informações |
| `[phones]`, `[contacts]` | Telefones ou todo o bloco de contatos configurado |
| `[soc-icons]` | Ícones de todas as redes sociais com links personalizados |
| `[tg]`, `[vk]`, `[whatsapp]`, `[youtube]`, `[instagram]`, `[max]` | Link de rede social com ícone e assinatura |

Coloque as fotos em `templates/images/myfotos/` ou `images/myfotos/`. A lista de arquivos mostra `zip`, `pdf`, `xls`, `xlsx`, `doc`, `docx`, `png`, `gif`, `jpg`, `jpeg`, `mp4`, `mov` e `mp3`. Para `[include]`, são suportados até dez níveis de aninhamento. A repetição da página no tópico atual é ignorada. Coloque os códigos dos componentes no corpo da página, em uma linha separada entre os blocos HTML.

Em “Configurações → Contatos e redes sociais” defina links, números, assinaturas e título do bloco. Em “Configurações → Tema e Cores”, estão disponíveis sistema, modos claro e escuro e paletas individuais. Em “Configurações → Componentes” você define os parâmetros gerais do carrossel e da galeria de fotos. Nas configurações há exemplos de inserção com valores selecionados. Os parâmetros no código da página substituem os valores comuns.

Em Tema e Cores, você pode salvar uma cópia das configurações selecionadas com seu próprio nome ou deixar o nome automático. “Aplicar” altera o design atual. Usar padrão aplica o tema salvo e lembra-o para o comando Redefinir tema para padrão. A remoção da lista preserva o design atual do site.

```text
[gal arrows=off numbers=off effect=fade fit=cover]/images/myfotos/[/gal]
[fotos columns=4 ratio=1x1 fit=cover captions=on links=off]/images/myfotos/[/fotos]
[files icons=on size=on order=desc download=on]/files/myfiles/[/files]
```

Para `[fotos]`, o número de colunas pode ser definido manualmente para uma tela ampla ou deixado selecionado automaticamente. Em telas estreitas, a grade reduz o número de colunas. As proporções disponíveis são 4:3, 1:1, 16:9 e original, modos `cover` e `contain`, assinaturas de nomes de arquivos e links para originais.

## Formulários e antispam

Na seção “Formulários” você pode editar `[form1]`, criar `[form2]` e os seguintes formulários. Para o formulário, são especificados o endereço do destinatário, a assinatura do botão e os campos. O campo pode ter um comprimento máximo. Um valor vazio usa o limite padrão para o tipo de campo. Ao inserir `89031234567`, o campo de telefone o formata como `+7(903)123-45-67`. O servidor verifica o comprimento, o tipo, as opções válidas e limita o tamanho total da solicitação. Os campos de entrada livre aceitam texto simples. O HTML nas mensagens é escapado na saída e as tags HTML nas novas mensagens são rejeitadas.

A caixa de seleção "Usar CAPTCHA" ativa o Google reCAPTCHA v2 com a verificação "Não sou um robô" para o formulário selecionado. Insira a chave do site e a chave secreta do domínio. O segredo é armazenado em um arquivo de configurações protegido e é usado apenas para verificação do servidor. [Documentação de verificação de servidor](https://developers.google.com/recaptcha/docs/verify).

Os formulários são enviados via AJAX. Os aplicativos são salvos na seção “Mensagens”. Se um destinatário for especificado, `mail()` será usado para notificações, o que requer correio configurado na hospedagem. Na seção Antispam você pode editar listas de palavras, expressões, endereços IPv4 e IPv6. Os links estão bloqueados no servidor. O endereço do remetente é retirado de `REMOTE_ADDR`. Atrás do proxy reverso, configure a recuperação do IP original no servidor web para proxies confiáveis.

Cada formulário possui um “Modelo de e-mail” com os campos “Para”, “Cc (Cc)”, “Assunto do e-mail” e “Corpo do e-mail”. Os endereços são indicados separados por vírgulas. As cópias serão enviadas se pelo menos um destinatário principal for especificado. As chaves de campo são inseridas no assunto e no texto entre chaves, por exemplo `Привет {name}!`. Um valor de campo vazio é substituído por uma string vazia, a chave desconhecida permanece na carta. Se o assunto ou texto estiver vazio, será utilizada a parte correspondente da letra padrão.

## Pedidos de contacto

Insira `[callback]` ou `[callback=Вызвать мастера]` na página HTML. O botão abrirá um formulário com um número de telefone. As solicitações são salvas na seção “Callbacks”. Lá também são definidas as assinaturas dos botões e o modo de transferência da solicitação para telefonia. Por padrão, o provedor “Nenhum” está selecionado e as solicitações são apenas salvas. A telefonia pode ser habilitada para envio manual da lista ou para envio imediatamente após o recebimento do número.

- **Zadarma.** Defina o número, SIP ou ramal da operadora no painel de administração e transfira as chaves para o PHP via `SITE_ZADARMA_KEY` e `SITE_ZADARMA_SECRET`. [Documentação da API Zadarma](https://zadarma.com/en/support/api/)
- **API HTTP.** Especifique `SITE_CALLBACK_WEBHOOK_URL` com o endereço HTTPS e opcionalmente `SITE_CALLBACK_WEBHOOK_TOKEN`. O site envia um POST com os campos JSON `id`, `phone`, `host`, `page`, `requested_at` e cabeçalho `Idempotency-Key`. O token é transferido como Portador. O código de resposta 2xx transfere a solicitação para o status “Aceita pela telefonia”.

Dê permissão de gravação ao processo PHP para `admin/callbacks/`. Os aplicativos usam configurações antispam de formulário. A repetição do mesmo número é limitada a um minuto; até três solicitações por 15 minutos são aceitas via IP.

## Blocos de informações e pasta de dados fechada

Na seção “Infoblocks” você pode criar conjuntos de dados, editar e arrastar linhas. `A–Z`, `a–z`, `0–9`, hífen e sublinhado são permitidos no nome do infoblock e nas chaves de coluna. Nomes e chaves são inseridos sem espaços. Os nomes das colunas e os valores das células podem ser escritos em russo. Os controles correspondem aos tipos de colunas e o salvamento é feito via AJAX. O botão ☰ “Editar como texto” muda todo o bloco de informações para CSV com um delimitador `|` e um botão copiar. As três primeiras linhas contêm as chaves, nomes e tipos das colunas. Depois, há linhas de dados. A data em CSV é `ГГГГ-ММ-ДД`, sim/não é armazenado como `1` ou `0`.

```text
name|phone|is_open
Название|Телефон|Открыто
text|tel|bool
Справочная|+7(903)123-45-67|1
```

O template `phones` utiliza colunas do tipo `tel` e uma coluna de texto com título. Você pode colocar seu template em `templates/local/iblock/имя.php`, onde `$block['columns']` e `$block['rows']` estão disponíveis.

O caminho `/data/myiblock.txt` dentro do código corresponde ao arquivo `data/myiblock.txt.php`. Este é um texto UTF-8 com um stub PHP que retorna 404 quando chamado diretamente. `router.php` fecha a pasta inteira ao executar localmente. `data/` possui regras para Apache e IIS. Para Apache, ative `data/.htaccess`. No Nginx, configure a pasta a ser fechada na configuração do site, levando em consideração o prefixo ao instalar em um subdiretório.

```nginx
location = /data { return 404; }
location ^~ /data/ { return 404; }
```

Após a instalação, verifique se uma solicitação direta ao `/data/` e o arquivo infoblock recebe 403 ou 404.

## Carregando fragmentos HTML

O endereço habitual `/contacts/` abre a página inteira. A solicitação `?ajax=1` retorna `<main>` com H1 e conteúdo processado se houver chave de sessão no cabeçalho `X-Site-Fragment`. Os fragmentos estão vinculados à origem atual. Páginas e fragmentos com chaves são servidos sem cache.

Em JavaScript em uma página do site, use a função integrada.

```js
const html = await window.siteFetchContent('/contacts/');
document.getElementById('content').outerHTML = html;
```

Ao instalar em um subdiretório, passe a URL com o prefixo do site. O script e os estilos são retirados da página que insere o fragmento. As páginas públicas permanecem acessíveis em endereços regulares.

## Modelos e recursos

Em “Configurações → Bibliotecas e fontes” jQuery, Bootstrap, Font Awesome, Material UI, Alpine.js, htmx, Chart.js e GLightbox são habilitados separadamente. Font Awesome é armazenado localmente. As bibliotecas restantes são baixadas pelo navegador da CDN quando ativadas. A UI do material está disponível nos scripts `type="module"` a `import { Button } from '@mui/material'`. Para ícones, `[fa-icon=house]` ou a marcação usual `<i class="fa-solid fa-house"></i>` é adequada.

10 fontes do Google com alfabeto cirílico estão disponíveis. A seleção substitui a fonte do sistema do site. O valor "Sistema" retorna o design anterior. Em “Configurações → Tema e Cores → Temas Salvos” existem cinco paletas prontas com opções claras e escuras.

| Caminho | Propósito |
| --- | --- |
| `index.php` | Página inicial e ponto de entrada geral |
| `contacts/index.php` | Exemplo de uma página em um diretório separado |
| `admin/` | Editor de login e página |
| `app/site.php` | Lendo páginas, construindo menus e títulos de sites em `SITE_NAME` |
| `templates/layout.php` | Layout geral, título da guia, metadados e área de conteúdo |
| `templates/header.php`, `menu.php`, `footer.php` | Cabeçalho, menu e rodapé |
| `templates/css/`, `js/`, `icons/`, `images/` | Estilos, scripts, ícones e imagens |
| `templates/components/`, `templates/iblock/` | Marcação de componentes e modelos de infoblock |
| `data/` | Arquivos de bloco de informações protegidos contra acesso HTTP direto |

O motor monta uma página completa de `[header]`, `[mobile-menu]`, `[menu]` e `[footer]`. São eles a barra superior, o menu móvel, o menu direito e o rodapé. Eles podem ser redefinidos por submodelos de mesmo nome no painel de administração, incluindo opções separadas para subdomínios. Armazene substituições de modelo PHP em `templates/local/` com o mesmo caminho relativo, por exemplo `templates/local/footer.php`.

## Atualização do motor

O arquivo completo é necessário para uma nova instalação. Para um site ativo, use `php-site-update.zip`. Ele contém apenas o mecanismo e os modelos padrão. Páginas salvas, blocos de informações, configurações, senhas, mensagens e extensões de usuário não são incluídos no arquivo.

Clique em “Atualizar mecanismo” em “Configurações → Atualizações” ou execute a mesma verificação no agendador de hospedagem.

```sh
php update.php --github
```

A fonte padrão é `bandaletov72/php-site-updates`. Para seu próprio repositório, configure-o como `SITE_GITHUB_REPO`. Um repositório público não requer um token. Para um repositório privado, passe a variável `SITE_GITHUB_TOKEN` para o processo PHP com acesso de leitura ao conteúdo. O servidor requer as extensões Zip e OpenSSL PHP e a capacidade de fazer solicitações HTTPS ao GitHub.

Para instalação manual, use ZIP.

```sh
php update.php /path/to/php-site-update.zip
```

Na versão antiga sem `update.php`, primeiro descompacte o pacote em cima do projeto, mantendo o `.htaccess` existente e os arquivos do `maintenance/`, se houver. Se você editou modelos padrão, transfira as edições para `templates/local/` antes de atualizar. O instalador verifica a lista e as somas de verificação dos arquivos e, para GitHub, também verifica a assinatura com a chave pública confiável `maintenance/release-public.pem`.

Para a próxima versão, altere a versão para `app/version.php` e construa o pacote via `php update.php --build php-site-update.zip`. Assine o ZIP com sua chave privada RSA com SHA‑256 e armazene a chave privada separadamente do site. Em seguida, envie o ZIP e `.sig` para o GitHub Release com a tag `vX.Y.Z` ou para `updates/vX.Y.Z/` e atualize `updates/latest.json` para `{"version":"X.Y.Z"}`.

## Extensões

Módulos de motor podem ser adicionados ao `app/extensions/` junto com atualizações. Armazene seus próprios módulos em `extensions/`, fora do pacote de atualização. O arquivo PHP retorna uma série de definições. Um único componente tem `kind` igual a `single` e um componente com argumento é igual a `wrapped`. O mecanismo reconhece novos códigos nas páginas existentes. Um componente pode declarar um menu de opções em `settings`, definindo cada opção como `label`, `default` e o array `options`. Os valores são recebidos em `$context['settings']`.

```php
<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

return ['notice' => [
    'kind' => 'wrapped',
    'render' => static function (string $text, array $context): string {
        return '<strong>' . site_escape($text) . '</strong>';
    },
]];
```

O componente de exemplo é inserido como `[notice]Важное сообщение[/notice]`.

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
