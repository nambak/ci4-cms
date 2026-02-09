# TODO: 홈페이지 남은 이슈

**마지막 업데이트**: 2026-02-09

---

## 완료된 P1 이슈

- [x] #25: Hero 뱃지 메시지 개선 → `PHP 8.x | 고성능 CMS` (PR #39)
- [x] #26: CTA 문구 통일 → `시작하기` (PR #39)
- [x] #27: CTA 섹션에 로그인 링크 추가 (PR #39)
- [x] #24: 모바일 네비게이션 햄버거 메뉴 구현 (`feature/mobile-hamburger-menu` 브랜치)

---

## 신규 작업: API 문서 페이지 네비게이션 추가

### 개요
`/docs/api-docs.html`에 `/home`과 동일한 상단 네비게이션 바 추가

### 현재 상태
- 정적 HTML 파일 (Redoc 기반)
- 네비게이션 없음 → 홈으로 돌아갈 수 없음

### 구현 방식 후보
1. **인라인 HTML/CSS** — api-docs.html에 직접 네비게이션 HTML + 인라인 CSS 추가. 별도 빌드 불필요, 독립적 동작
2. **output.css 링크 + HTML** — 기존 Tailwind output.css를 링크하고 home.php와 동일한 마크업 사용. 일관성 높지만 CSS 파일 의존성
3. **PHP 전환** — api-docs를 PHP 뷰로 전환하여 네비게이션 partial 공유. 가장 일관성 높지만 라우트 수정 필요

### 작업 시 주의사항
- Redoc의 `scrollYOffset` 값을 navbar 높이에 맞게 조정 필요 (현재 50px)
- 모바일 햄버거 메뉴도 동일하게 적용
- Nord 테마 일관성 유지

---

## P2 이슈 (검토 필요)

### #28: Features/Architecture 섹션 배경 차별화
- 두 섹션 배경색이 비슷해 시각적 구분 불명확
- 옵션: 그라데이션 / 대비 강화 / 분리선

### #29: 통계 숫자 카운트업 애니메이션 추가
- CTA 섹션 통계에 스크롤 진입 시 카운트업 효과
- CountUp.js 또는 Intersection Observer + Vanilla JS

### #30: Footer 확장 - 소셜미디어, 문의처, 법적 문서
- 소셜 링크, 연락처, 이용약관/개인정보처리방침 추가
- 신규 파일 필요: `app/Views/legal/terms.php`, `privacy.php`

### #31: API 문서 링크에 외부 링크 아이콘 추가
- 외부 링크에 SVG 아이콘 표시 + `rel="noopener noreferrer"`

---

## 참고 자료

### 관련 파일
- `app/Views/home.php` - 홈페이지 메인 파일
- `public/docs/api-docs.html` - API 문서 페이지
- `src/input.css` - CSS 소스
- `tailwind.config.js` - Tailwind + DaisyUI 설정

### Nord Theme 색상
- `nord-2`: #434c5e
- `nord-3`: #4c566a
- `nord-9`: #81a1c1
- `nord-10`: #5e81ac
