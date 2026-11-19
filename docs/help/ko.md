# 한국어 · 1.6.0

## 검색과 캐시

페이지를 게시하거나 관리자 화면 또는 FTP에서 수정한 경우 유지 관리에서 텍스트 색인을 다시 생성하세요. 추천 검색은 방문자 언어의 공개 페이지를 사용합니다. 캐시는 기본적으로 꺼져 있으며 같은 곳에서 설정하거나 지울 수 있습니다. 양식과 동적 요소는 요청마다 처리합니다.

## 사이트에서 편집

로그인 후 상단 바 오른쪽 스위치로 사이트 전체 편집 모드를 켭니다. 구성 요소 옆의 링크에서 옵션을 수정할 수 있습니다. 페이지 CSS와 JS는 라이트박스에서 엽니다. 관리자 화면에서 HTML, 시각 편집, 코드 삽입, AJAX 일괄 업로드와 정렬을 사용할 수 있습니다.

## 구성 요소와 탐색

지정하지 않은 값은 공통 설정을 따릅니다. width에는 100, 50, 33, 25와 선택적인 %를 쓸 수 있습니다. 모바일에서는 한 열로 표시됩니다. numbers=off로 번호를 숨기고 좌우로 밀어 사진을 넘길 수 있습니다. 모바일 메뉴는 오른쪽에서 열리며 경로 탐색은 기본 설정에서 끌 수 있습니다.

```text
[gal width=50 numbers=off arrows=on fit=cover]/images/myfotos/[/gal]
[fotos width=50% columns=3 captions=on]/images/myfotos/[/fotos]
[files width=33 icons=off size=on download=on]/files/myfiles/[/files]
```

## 언어·디자인·가격

지역 설정에서 관리자 및 사이트 언어, 통화, 숫자·날짜·시간 형식과 시간대를 고릅니다. 번역은 index.php 옆의 index_en.php에 저장하며, 번역이 없으면 기본 콘텐츠를 표시합니다. 가격 필드는 선택한 통화를 사용합니다. 테마 색상과 모드를 미리 보고 밝은·어두운 로고를 따로 업로드하거나 Roboto Condensed를 사용자 문구로 확인할 수 있습니다.

```text
/contacts/index.php → /contacts/index_en.php
```

## 양식·요청·연동

CAPTCHA 설정에서 Google reCAPTCHA v2 공통 키를 관리하고 양식별로도 설정할 수 있습니다. 메시지와 콜백 요청은 읽지 않은 항목부터 한 페이지에 20개씩 표시됩니다. 연동에서 Zadarma, Telegram, 분석 도구 및 Google 또는 Yandex 지도와 클러스터를 설정합니다. 라이브러리 라이트박스에서 현재 버전을 테스트하고 삽입 코드를 확인합니다. CodeMirror에는 자동, HTML, CSS, JS, PHP, RAW 모드가 있습니다.

```text
[map lat=55.7558 lng=37.6173 title="Office" zoom=13 provider=yandex url="/contacts/" button="Open"]
```

```text
[callback button="Call me" title="Callback" submit="Send" placeholder="+7(900)123-45-67"]
```

## PHP 모듈

PHP 모듈에서 이름, 설명, 삽입 가능 여부를 수정합니다. server-date는 지역 날짜 형식을 사용합니다. 실행 파일은 개발자가 보호된 디렉터리에 설치합니다.

```text
[php]server-date[/php]
```

