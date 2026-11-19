# Português · 1.6.0

## Pesquisa e cache

Reconstrua o índice de texto em Manutenção após publicar ou editar páginas pelo painel ou via FTP. As sugestões usam páginas públicas no idioma do visitante. O cache vem desativado e pode ser ligado ou limpo ali. Formulários e componentes dinâmicos são processados a cada solicitação.

## Edição no site

Após o login, o controle à direita da barra ativa os links de edição de componentes em todo o site. CSS e JS da página abrem em uma janela modal. No painel, use HTML, edição visual, inserção de códigos e envio em lote e ordenação de arquivos por AJAX.

## Componentes e navegação

Parâmetros ausentes usam a configuração global. width aceita 100, 50, 33 ou 25, com ou sem %. Em celulares os blocos ficam em uma coluna. numbers=off oculta o contador. Deslize para mudar de foto. O menu móvel abre pela direita e as trilhas de navegação podem ser desativadas nos ajustes gerais.

```text
[gal width=50 numbers=off arrows=on fit=cover]/images/myfotos/[/gal]
[fotos width=50% columns=3 captions=on]/images/myfotos/[/fotos]
[files width=33 icons=off size=on download=on]/files/myfiles/[/files]
```

## Idiomas, aparência e preços

Nas opções regionais, escolha idiomas, moeda, formatos e fuso horário. Coloque index_en.php ao lado de index.php para adicionar uma tradução; sem o arquivo, o conteúdo original aparece. O campo Preço usa a moeda escolhida. Veja as paletas, altere o modo em tempo real, envie os logotipos claro e escuro e teste Roboto Condensed com uma frase própria.

```text
/contacts/index.php → /contacts/index_en.php
```

## Formulários, solicitações e integrações

Defina chaves do Google reCAPTCHA v2 globais ou por formulário. Mensagens e retornos têm 20 itens por página, com os não lidos no topo. Integrações inclui Zadarma, Telegram, métricas e mapas Google ou Yandex com agrupamento opcional. A janela das bibliotecas testa a versão ativa e traz o código. CodeMirror tem Auto, HTML, CSS, JS, PHP e RAW.

```text
[map lat=55.7558 lng=37.6173 title="Office" zoom=13 provider=yandex url="/contacts/" button="Open"]
```

```text
[callback button="Call me" title="Callback" submit="Send" placeholder="+7(900)123-45-67"]
```

## Módulos PHP

Em Módulos PHP, defina o nome, a descrição e a disponibilidade. server-date usa a data em formato regional. Os desenvolvedores instalam os arquivos executáveis em uma pasta protegida.

```text
[php]server-date[/php]
```

