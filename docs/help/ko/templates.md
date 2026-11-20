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

### 홈 템플릿 및 정책

[page-content]는 홈 화면 내용을 [home]에 넣습니다. 페이지 메뉴에서 /privacy/를 열어 정책을 편집할 수 있습니다. [site-name]과 [site-url]은 기본 설정에서 가져옵니다.

```text
[page-content]
```

```text
[site-name] · [site-url]
```

### 바닥글의 링크 및 설정

[privacy]는 /privacy/로 연결되고 [top]은 맨 위로 이동하며 [cookies]는 동의 설정을 엽니다.

```text
[privacy]
[cookies]
[top]
```

### 일반적인 PHP 함수

"디자인 템플릿 → 기능" 섹션에서 functions.php를 편집합니다. PHP 코드에서 translit() 또는 ew_call()는 등록된 함수를 호출합니다. 단축 코드 [func=translit]는 콘텐츠 및 "사용자 정의 HTML" 필드에서 사용할 수 있습니다.

```text
[func=translit value="Москва и München"]
```

```text
$var = translit("모스크바");
```

```text
echo ew_escape(ew_call("translit", "München"));
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

## 관리자 화면 상단 버튼 · 1.6.4

지구본 아이콘은 사이트를 열고 화살표 아이콘은 로그아웃합니다. 휴대전화에서는 아이콘만 표시됩니다. 마우스를 올리거나 키보드로 초점을 맞추면 이름을 볼 수 있습니다.

콘텐츠, 양식 및 요청, 사이트 및 서비스는 바로 열립니다. 기본 메뉴 아래에 선택한 섹션의 탭이 표시됩니다.

홈 화면(index.php)이나 템플릿의 HTML에 [bread-crumbs] 또는 [search]를 넣으면 지정한 위치에 한 번씩 표시됩니다. 탐색 경로와 검색 설정이 적용됩니다. [privacy]는 /privacy/로 연결됩니다. 정책 문구는 페이지 메뉴에서 편집할 수 있습니다.

```text
[bread-crumbs]
[search]
[privacy]
```

## 설정 파일, 지도 및 서식 · 1.6.5

유지 관리의 설정 파일에서 .htaccess, robots.txt, sitemap.xml을 편집합니다. 생성 시 사이트맵에 포함하도록 지정한 공개 페이지만 사용합니다. 이전 .htaccess 버전을 복원할 수 있습니다.

위도와 경도를 입력하세요. [map]은 현재 페이지 위치를 표시하고 [maps]는 좌표가 있는 공개 페이지를 모읍니다. center와 zoom으로 위치와 배율을 설정합니다. 연동 메뉴에서 지도 제공사를 선택합니다.

[main-menu]는 헤더 아래의 펼침 메뉴입니다. 사용자 서식과 기기별 표시 여부를 설정할 수 있습니다. 조각 메뉴에서 도메인 추가 양식을 열 수 있습니다. 개인정보 처리방침 링크에는 [privacy]를 사용하세요.

연동 메뉴에서 JivoSite, Microsoft Clarity, Umami Cloud ID, Botfaqtor 코드 또는 Slack 웹훅을 설정하세요. 각 필드 옆에 설명 링크가 있습니다. Zadarma에서 잔액을 확인하거나 SMS와 통화를 테스트할 수 있습니다. 테스트에는 요금이 발생할 수 있습니다. [payment]에 YooMoney, PayPal, SBP 결제 링크를 설정하고 결제 상태는 제공사에서 확인하세요.

HTML 편집기와 시각적 편집기의 아이콘으로 글자 서식, H1–H5 제목 및 목록을 설정할 수 있습니다. 커서를 놓고 서식, 컴포넌트, 이미지 또는 아이콘을 선택하세요. 저장된 양식과 컴포넌트 옵션을 이용할 수 있습니다. Font Awesome은 라이브러리를 활성화하면 사용할 수 있습니다.

폴더 이름을 누르면 오른쪽에서 섹션을 관리할 수 있습니다. 상위 섹션, 데스크톱과 모바일 메뉴 또는 이동 URL을 설정하세요. 빈 섹션은 하위 항목을 표시합니다. 유지 관리에서 /contact.html => /contacts/ 규칙을 추가하고 /error/ 페이지를 수정할 수 있습니다. 모바일에서는 언어와 검색이 아이콘으로 표시됩니다.

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

## 페이지에서 컴포넌트 데이터 편집 · 1.6.6

편집 모드에서 정보 블록, [formX] 양식, [tel] 조각 등은 데이터와 전체 화면 메뉴를 제공합니다. 보기 유형 목록에는 등록된 서식이 표시됩니다. 루트 폴더는 섹션을 최상위로 옮깁니다. [list-pages]는 섹션과 하위 폴더의 공개 페이지를 나열합니다. 언어 메뉴는 바깥을 클릭하거나 Escape 키를 누르면 닫힙니다.

## 페이지 설정 · 1.6.8

편집에서 콘텐츠를 작성하세요. 주소, 메뉴, 공개 일정은 페이지 설정에 있습니다. SEO에는 제목, 설명, 키워드, 사이트맵 포함 여부가 있습니다. 지도에는 좌표와 태그 예제가 있습니다.

날짜를 비워 두면 기간 제한이 없습니다. 사이트의 시간대를 사용합니다. 숨긴 페이지는 별도로 공개해야 합니다.

- `[map]` — 현재 페이지의 위치를 표시합니다.
- `[maps]` — 공개된 모든 페이지의 위치를 표시합니다.

## 배너 및 이미지 편집기 · 1.6.8

구성 요소에서 세트와 슬라이드를 관리하세요. 폴더의 사진을 사용하고 설명과 링크, 양식 또는 콜백 버튼을 추가하고 표시 기간을 설정할 수 있습니다. 편집 모드에서는 슬라이드 작업이 표시됩니다. 이미지 선택기에서 폴더 탐색, 업로드, 삭제 및 GD를 사용한 회전 또는 크기 조정 사본을 만들 수 있습니다. Font Awesome 아이콘은 이름으로 검색할 수 있습니다.

```text
[banners]
[banners set=summer arrows=on numbers=off]
[banners]/images/myfotos/[/banners]
```

## 언어 · 1.6.9

언어 탭에는 페이지의 언어가 표시됩니다. 열기를 누르면 저장된 버전으로 이동합니다. 번역 만들기는 빈 편집기를 엽니다.

하위 섹션은 현재 섹션 안에, 같은 수준의 섹션은 그 옆에, 최상위 섹션은 사이트의 맨 위 수준에 생성됩니다.

## PHP 호환성과 탐색 · 1.6.10

최소 버전은 PHP 7.2입니다. 사이트와 관리자 화면은 PHP 8.6을 지원합니다. 사용자 정의 모듈과 템플릿은 서버에 설치된 버전의 문법을 사용하세요.

[bread-crumbs]의 현재 항목을 펼치면 주 메뉴에 있는 같은 수준의 공개 섹션을 볼 수 있습니다. 설정한 URL은 메뉴, 섹션 목록, 탐색 경로, 검색 결과와 제안에 사용됩니다.

휴지통은 인포블록 편집기에서 행을 제거합니다. 열을 제거하면 해당 열의 값도 모두 사라집니다. 변경 사항은 저장할 때 기록되며 순서 변경 뒤의 자동 저장도 포함됩니다.

### 사이트 백업

비공개 ZIP에는 페이지, 미디어, 인포블록, 설정, 템플릿, PHP 모듈이 들어갑니다. 관리자 계정과 요청은 별도로 보관됩니다. PHP Zip이 필요합니다.

콘텐츠와 설정을 교체합니다. 먼저 현재 상태를 백업합니다.

### 사용하지 않는 파일 정리

저장된 콘텐츠에서 참조되지 않는 미디어를 찾습니다. 목록을 검토하고 파일을 선택하세요. 삭제 전 백업을 만듭니다. 구성 요소 코드를 제거해도 폴더와 파일은 유지됩니다.

### 지도에서 지점 선택

Yandex Maps 콘솔에서 «JavaScript API» 키를 만들고 HTTP Referer로 제한하세요. 페이지의 지도 탭에서 지점을 선택한 뒤 좌표를 적용하세요.

텍스트를 선택하여 정렬하거나 서식을 지웁니다. 이미지를 클릭해 속성을 열고 끌어서 이동하거나 모서리로 크기를 조절합니다. URL, 이메일, 전화번호, 앵커를 링크할 수 있습니다. 버튼은 URL, 양식, 콜백 또는 결제 링크를 엽니다.

HTML과 시각 편집기 탭에서 모드를 전환합니다. SUP, SUB, 문단 버튼은 해당 요소를 삽입합니다. 구성 요소 이름 옆에 코드가 표시됩니다. 삽입할 때 없는 폴더를 만듭니다. 폴더 이름에는 영문, 숫자, 하이픈, 밑줄을 사용할 수 있습니다. 언어 탭에서 사이트 언어 설정으로 이동합니다.

```text
[button action="url" label="Contacts" url="/contacts/"]
[button action="form" label="Write to us" form="form1"]
[button action="callback" label="Call me"]
[button action="payment" label="Pay" amount="1250.00" currency="RUB" yoomoney="410010000000000"]
[map]
[maps]
```

## 판매 · 1.6.11

판매 탭에서 가격과 결제 수단을 설정하거나 언어별로 변경하세요. [item-price]와 [item-buy-button]을 삽입합니다. 결제 전에 주문을 기록합니다. 상태 알림은 이메일이나 Zadarma SMS로 보낼 수 있습니다.

주문 접수 · 결제 확인 · 발송 완료 · 종료 · 취소

```text
[item-price]
[item-buy-button]
```

이미지를 삽입하려면 파일이나 URL을 선택하세요. 이미지를 클릭하면 크기, 정렬, 네 방향 여백 및 CSS를 설정할 수 있습니다. 이미지 정렬은 텍스트와 독립적입니다. HTML에서는 블록이 줄별로 표시됩니다. [list-pages]는 구성 요소 목록에 있습니다. CMS 로고는 테마 색상을 사용합니다.

## 데이터 블록 차트

페이지 편집기에서 구성 요소 삽입을 열고 표 또는 차트를 선택합니다. 차트를 사용하려면 Chart.js를 활성화하고 레이블 열과 숫자 또는 가격 열을 선택합니다. 원형, 도넛, 극좌표 차트에는 음수가 아닌 값 계열 하나가 필요합니다. 라이브러리를 끄면 표가 표시됩니다.

편집기를 다시 열면 저장한 양식, 템플릿, 스니펫과 등록한 구성 요소가 삽입 목록에 표시됩니다. 새 배너 세트 ID에는 A–Z, a–z, 하이픈, 밑줄을 쓸 수 있습니다. [banners] 폴더에 이미지를 올리면 각 파일 옆에 슬라이드 편집 링크가 표시됩니다. 편집 모드에서는 선택한 슬라이드만 대화상자에 열리고 기본 설정은 별도 링크로 이동합니다. 캐러셀, 갤러리, 파일 목록의 저장 옆 파일 관리자에서 업로드, 삭제, 정렬, 하위 폴더 생성(A–Z, a–z, 0–9, -, _)을 할 수 있습니다. 슬라이드 색상은 사각형 견본으로 표시됩니다.

```text
[iblock=table]/data/myiblock.txt[/iblock]
[iblock=charts type=line labels=month values=sales,orders]/data/myiblock.txt[/iblock]
[banners set=summer-promo]
```

## 관리자 머리글 및 바닥글 · 1.6.12

데스크톱에서는 로고 옆에 Eurowebcart CMS와 Light, 설치 버전이 표시됩니다. 휴대폰에서는 로고와 버튼이 같은 줄에 있습니다. 바닥글에 저장소 주소가 있으며 설정에서 누적 업데이트를 확인할 수 있습니다.

## 관리자 머리글 및 바닥글 · 1.6.13

데스크톱에서는 로고 옆에 Eurowebcart CMS와 Light, 설치 버전이 표시됩니다. 휴대폰에서는 로고와 버튼이 같은 줄에 있습니다. 바닥글에 저장소 주소가 있으며 설정에서 누적 업데이트를 확인할 수 있습니다. 페이지 목록의 기본 홈 이름은 관리자 언어로 표시됩니다. 바닥글 로고에는 Eurowebcart CMS와 LIGHT가 두 줄로 표시됩니다.

## PHP 정보

유지보수의 phpinfo()에서 로그인한 관리자는 PHP 버전, 모듈 및 구성을 확인할 수 있습니다. 환경 변수와 요청 변수는 표시하지 않습니다. 기본 설정에서 error_reporting을 선택할 수 있으며 초기값은 서버 설정입니다.
