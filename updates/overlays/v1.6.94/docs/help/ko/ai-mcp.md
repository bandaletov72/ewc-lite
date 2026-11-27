# AI: ChatGPT 연결

사이트 편집기의 연결 방식을 선택하세요. 나중에 AI 탭에서 설정할 수 있습니다.

## Secure MCP Tunnel로 ChatGPT 연결

1. OpenAI Platform에서 사이트 전용 터널과 Tunnels Read + Use 키를 만드세요. 여기에는 터널 ID만 입력하세요.
2. MCP 패키지와 OpenAI 공식 클라이언트를 설치하세요. 비공개 초안 폴더를 설정하고 서비스 비밀 저장소에 키를 보관하세요. 실행: `instance-from-cms.sh`.
3. ChatGPT에서 사이트 이름, Tunnel 모드, 동일한 ID로 사용자 지정 MCP 서버를 추가하세요. list_pages로 확인하세요.

[Secure MCP Tunnel](https://developers.openai.com/api/docs/guides/secure-mcp-tunnels)

## EWC API 직접 연결 (터널 없음)

자체 클라이언트는 /mcp를 HTTPS로 노출하고 전용 EWC 토큰을 Authorization: Bearer로 전송합니다. 기본값은 읽기 전용입니다.

ChatGPT에는 EWC 토큰을 입력할 수 없습니다. 비공개 편집기에 직접 연결하려면 포함되지 않은 호환 OAuth 2.1이 필요합니다. [OAuth](https://developers.openai.com/plugins/build/auth)

## 편집 확인

`list_pages` → `draft_page` → `preview_draft` → 명시적 승인 → `publish`.

관리 화면에서 페이지를 열고 미리보기와 비교하세요. 읽기 전용 모드에서는 편집할 수 없습니다.
