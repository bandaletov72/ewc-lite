# 템플릿 및 구성 요소

## 레이아웃 템플릿

### 머리글, 메뉴 및 바닥글

전체 페이지는 이러한 블록을 자동으로 연결합니다. 일반 버전은 "디자인 템플릿"에서 편집되고 개별 도메인의 경우 "도메인" 섹션에서 편집됩니다.

```text
[header]
[mobile-menu]
[menu]
[footer]
```

### 다른 페이지 삽입

게시된 페이지의 콘텐츠를 삽입합니다. 최대 중첩은 10레벨입니다. 동일한 체인의 페이지 반복은 건너뜁니다.

```text
[include]/contacts/[/include]
```

### 메뉴의 링크

"메뉴에 표시" 확인란을 선택하면 표준 메뉴에 대한 링크가 추가됩니다. 이름이 비어 있으면 제목이 사용됩니다. 폴더 페이지의 확인란을 선택 취소하면 해당 하위 페이지도 메뉴에서 사라집니다.

```text
메뉴 표시: 1
메뉴: 연락처
```

### CSS와 자바스크립트

일반 코드는 “CSS 및 JS” 탭에서 편집됩니다. 페이지 코드는 "CSS 및 JavaScript 페이지" 블록에서 편집되어 일반 페이지 뒤에 포함됩니다. CSS 및 JavaScript 콘텐츠를 필드에 붙여넣습니다.

```text
.page-content { max-width: 72rem; }
```

```text
document.documentElement.classList.add('custom-script-loaded');
```

### 서비스 헤더

“디자인 템플릿 → 서비스 헤더” 섹션에서는 헤드 내부 또는 닫는 본문 앞에 일반 메타 태그와 카운터 코드를 삽입할 수 있습니다. 정식은 전체 페이지에 자동으로 추가됩니다.

```text
<meta name="theme-color" content="#065f63">
```

### 글꼴 멋진 아이콘

Font Awesome Free 7.3.1이 연결되었습니다. 짧은 코드는 콘텐츠 및 HTML 디자인 블록에서 작동합니다. 솔리드, 레귤러, 브랜드 스타일이 가능합니다. 작업을 나타내는 경우 아이콘 옆에 텍스트를 추가하세요.

```text
[fa-icon=house] [fa-icon=regular:envelope] [fa-icon=brands:github]
```

```text
<i class="fa-icon fa-solid fa-phone" aria-hidden="true"></i> 연락처
```

### 라이브러리 및 글꼴

연결은 “설정 → 라이브러리 및 글꼴”에서 관리됩니다. 머티리얼 UI의 경우 모듈식 JavaScript를 사용하세요. 선택한 Google 글꼴이 사이트의 시스템 글꼴을 대체합니다.

```text
<script type="module">import { Button } from "@mui/material";</script>
```

```text
GLightbox({selector: ".glightbox"});
```

## 변수, 하위 템플릿 및 날짜

### 사이트 변수

"설정 → 일반"에 항목을 추가합니다. 텍스트, 머리글, 사이트 제목 또는 바닥글에 키를 삽입하세요.

```text
[city] = 모스크바
[spb] = 상트페테르부르크
```

```text
시내 서비스 [city]
```

### 스니펫

하위 템플릿 섹션에서 이름, 설명 및 값을 지정합니다. 도메인의 경우 해당 값을 먼저 가져온 다음 일반 값을 가져옵니다. 하위 템플릿에는 HTML이 포함될 수 있습니다. 헤더의 마크업이 제거됩니다.

```text
[tel]
```

```text
[title]
```

### 날짜 및 시간

시간대는 PHP에서 가져옵니다. [year] - 연도, [month] - 월, [day] - 일, [time] - 시간 및 분, [dmY] 및 [date] - 날짜 DD.MM.YYYY, [datetime] - 날짜 및 시간.

```text
© [year][city]
날짜 [dmY], 시간 [time]
```

### CMS 이름 및 작성자

코드 [author]는 Eurowebcart CMS Light, 작성자 이름 Бандалетов Павел 및 GitHub에 대한 링크를 출력합니다. 표준 바닥글에는 서명이 자동으로 나타납니다. 자신의 HTML 바닥글에서 원하는 위치에 코드를 붙여넣습니다.

```text
[author]
```

## 회전판, 사진 갤러리 및 파일

### 회전목마 [gal]

폴더의 사진을 표시합니다. 화살표와 숫자는 켜기 또는 끄기, 효과 – 슬라이드, 페이드 또는 없음, 맞춤 – 포함 또는 덮기를 허용합니다.

```text
[gal]/images/myfotos/[/gal]
```

```text
[gal arrows=on numbers=off effect=fade fit=cover width=50]/images/myfotos/[/gal]
```

### 포토갤러리 [fotos]

반응형 그리드를 표시합니다. 열 — 자동 또는 1에서 6까지의 숫자, 비율 — 4x3, 1x1, 16x9 또는 원본, 맞춤 — 덮거나 포함합니다. 캡션과 링크는 켜기 또는 끄기를 통해 활성화됩니다.

```text
[fotos]/images/myfotos/[/fotos]
```

```text
[fotos columns=4 ratio=1x1 fit=cover captions=on links=off width=50%]/images/myfotos/[/fotos]
```

### 파일 목록 [files]

지원되는 파일을 나열합니다. 아이콘, 크기 및 다운로드 허용 여부, 주문 – asc 또는 desc. Zip, pdf, xls, xlsx, doc, docx, png, gif, jpg, jpeg, mp4, mov 및 mp3가 허용됩니다.

```text
[files]/files/myfiles/[/files]
```

```text
[files icons=on size=on order=desc download=on width=33]/files/myfiles/[/files]
```

## 양식 및 콜백

### 양식 [formN]

"양식" 섹션에서 필드, 최대 길이, 버튼 텍스트 및 수신자를 구성합니다. "CAPTCHA 사용"을 선택하면 서버에서 응답 확인과 함께 Google reCAPTCHA v2 위젯이 활성화됩니다. 요청은 AJAX를 통해 전송되며 메시지 섹션에 표시됩니다.

```text
[form1]
[form2]
```

### 메일 양식 템플릿

제목과 텍스트는 각 양식마다 별도로 저장됩니다. 이메일 템플릿에서 중괄호 안에 필드 키를 삽입하세요. 수신자 및 사본 목록은 쉼표로 구분됩니다.

```text
안녕하세요 {name}!
전화: {phone}
메시지: {message}
```

### 다시 전화하세요 [callback]

전화번호가 포함된 양식을 엽니다. 애플리케이션은 "콜백" 섹션에 저장됩니다. 버튼 라벨은 코드에서 설정할 수 있습니다. "아니요" 공급자를 사용하면 전화 통신으로의 전송이 비활성화됩니다.

```text
[callback]
[callback=Вызвать мастера]
```

## 데이터 블록

### 테이블 및 전화 목록

"Infoblocks" 섹션에 데이터 세트를 생성합니다. 인포블록 이름과 열 키에는 A–Z, a–z, 0–9, 하이픈 및 밑줄이 허용됩니다. 공백은 허용되지 않습니다. 테이블 템플릿에는 테이블이 표시되고, 전화기에는 통화 링크가 포함된 전화 열이 표시됩니다. 종료 코드 [iblock] 및 [/iblock]가 지원됩니다.

```text
[iblock=table]/data/myiblock.txt[iblock]
[iblock=phones]/data/myiblock.txt[/iblock]
```

## 연락처 및 소셜 미디어

### 일반 블록 및 아이콘

[contacts]는 연락처 블록을 표시하고, [phones]는 전화번호를 표시하며, [soc-icons]는 맞춤형 링크를 통해 모든 소셜 네트워크의 아이콘을 수집합니다.

```text
[contacts]
[phones]
[soc-icons]
```

### 개인 소셜 네트워크

각 코드는 소셜 네트워크에 대한 링크를 표시합니다. 주소와 서명은 '설정 → 연락처 및 소셜 네트워크'에서 설정됩니다.

```text
[tg] [vk] [whatsapp] [youtube] [instagram] [max]
```

```text
[wa] [yt] [ig]
```

