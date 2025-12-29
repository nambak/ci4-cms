# CI4 CMS

CodeIgniter 4 기반 멀티테넌시 CMS 플랫폼입니다. URL 기반 테넌트 분리, Shield RBAC 권한 관리, DaisyUI 프론트엔드를 특징으로 합니다.

> **학습 목적의 개인 포트폴리오 프로젝트입니다.**

## 주요 기능

- **멀티테넌시**: URL 기반 테넌트 분리 (yoursite.com/tenant-slug)
- **권한 관리**: Shield 패키지를 활용한 RBAC 시스템
- **콘텐츠 관리**: 포스트, 페이지, 카테고리, 태그 관리
- **댓글 시스템**: 댓글 작성, 모더레이션, 대댓글 기능
- **미디어 라이브러리**: 파일 업로드 및 관리
- **SEO 관리**: 메타 태그, URL slug 관리
- **프론트엔드**: 공개 페이지, 포스트 필터링, 댓글 표시

## 기술 스택

- **Backend**: CodeIgniter 4
- **인증 & 권한**: Shield (CodeIgniter 공식 RBAC)
- **Frontend**: DaisyUI + Tailwind CSS
- **Database**: MySQL/MariaDB
- **버전 관리**: Git

## 빠른 시작

### 요구사항
- PHP 8.1+
- Composer
- MySQL/MariaDB
- Node.js (Tailwind CSS 빌드용)

### 설치

```bash
# 저장소 클론
git clone https://github.com/nambak/ci4-cms.git
cd ci4-cms

# Composer 의존성 설치
composer install

# Node.js 패키지 설치
npm install

# 환경 설정
cp env .env

# 데이터베이스 설정 (.env 파일에서)
# DB_HOST, DB_NAME, DB_USER, DB_PASS 설정

# 마이그레이션 실행
php spark migrate

# 시드 데이터 로드 (선택)
php spark db:seed SampleSeeder

# Tailwind CSS 빌드
npm run build
```

### 실행

```bash
# 개발 서버 실행
php spark serve

# CSS watch 모드 (별도 터미널)
npm run dev

# 접속
http://localhost:8080
```

## 프로젝트 구조

```
ci4-cms/
├── app/
│   ├── Controllers/     # 컨트롤러
│   ├── Models/          # 모델
│   ├── Views/           # 뷰
│   ├── Filters/         # 필터 (인증, 테넌트)
│   └── Database/        # 마이그레이션, 시드
├── public/              # 공개 폴더
├── writable/            # 쓰기 가능 폴더 (로그, 캐시)
├── tests/               # 테스트
├── .env                 # 환경 설정
├── composer.json        # PHP 의존성
└── package.json         # Node.js 의존성
```

## 개발 로드맵

### Phase 1: 기본 구조 (진행 중)
- [ ] 프로젝트 초기 설정
- [ ] 데이터베이스 설계 및 마이그레이션
- [ ] 테넌트 관리 기능
- [ ] 사용자 & 권한 관리

### Phase 2: 핵심 기능
- [ ] 포스트/페이지 CRUD
- [ ] 카테고리/태그 관리
- [ ] 댓글 시스템
- [ ] 미디어 라이브러리

### Phase 3: 고급 기능
- [ ] SEO 관리
- [ ] 프론트엔드 공개 페이지
- [ ] 대시보드 및 통계
- [ ] 이메일 알림

### Phase 4: 최적화
- [ ] 테스트 작성
- [ ] 성능 최적화
- [ ] 보안 강화
- [ ] 배포 가이드

## 테스트

본 프로젝트는 OpenAPI 스펙 기반의 API 테스트를 포함하고 있습니다.

### 테스트 실행

```bash
# 전체 테스트 실행
composer test

# 또는 PHPUnit 직접 실행
vendor/bin/phpunit

# 특정 테스트만 실행
vendor/bin/phpunit tests/api/AuthenticationApiTest.php

# 코드 커버리지 포함
vendor/bin/phpunit --coverage-html build/coverage
```

### 테스트 구조

```
tests/
├── api/                           # API 테스트
│   ├── AuthenticationApiTest.php  # 인증 API 테스트 (5개 테스트)
│   ├── PostsApiTest.php           # 포스트 API 테스트 (15개 테스트)
│   ├── CategoriesApiTest.php      # 카테고리 API 테스트 (7개 테스트)
│   └── CommentsApiTest.php        # 댓글 API 테스트 (9개 테스트)
├── unit/                          # 단위 테스트
└── database/                      # 데이터베이스 테스트
```

### 테스트 개요

- **총 테스트 수**: 36개 이상
- **테스트 종류**: API 통합 테스트, 단위 테스트
- **커버리지**: OpenAPI 스펙 기반 주요 엔드포인트 커버
- **테스트 프레임워크**: PHPUnit 10

### 주요 테스트 항목

| 테스트 파일 | 테스트 내용 |
|-----------|----------|
| **AuthenticationApiTest** | 사용자 등록, 로그인, 로그아웃, 토큰 갱신, 현재 사용자 조회 |
| **PostsApiTest** | 포스트 CRUD, 페이지네이션, 필터링, 검색, 발행 |
| **CategoriesApiTest** | 카테고리 CRUD, 카테고리별 포스트 조회 |
| **CommentsApiTest** | 댓글 CRUD, 대댓글, 모더레이션 |

### 데이터베이스 설정

테스트 실행 전 `phpunit.xml.dist` 파일에서 테스트용 데이터베이스를 설정하세요:

```xml
<env name="database.tests.hostname" value="localhost"/>
<env name="database.tests.database" value="ci4_test"/>
<env name="database.tests.username" value="root"/>
<env name="database.tests.password" value=""/>
<env name="database.tests.DBDriver" value="MySQLi"/>
```

## 프론트엔드 개발

이 프로젝트는 **Nord Theme** 색상 팔레트를 기반으로 한 **Tailwind CSS**와 **DaisyUI** 컴포넌트 라이브러리를 사용합니다.

### CSS 빌드

개발 중에는 watch 모드로 실행하여 파일 변경 시 자동으로 CSS를 다시 빌드합니다:

```bash
# 개발 모드 (watch mode)
npm run dev
```

프로덕션 배포를 위한 최적화된 빌드:

```bash
# 프로덕션 빌드 (minified)
npm run build
```

### 파일 구조

```
ci4-cms/
├── src/
│   └── input.css              # Tailwind CSS 입력 파일
├── public/
│   └── assets/
│       └── css/
│           └── output.css     # 빌드된 CSS 파일 (생성됨)
├── app/
│   └── Views/
│       └── home.php           # 홈페이지 뷰
├── tailwind.config.js         # Tailwind 설정 (Nord 테마 포함)
├── postcss.config.js          # PostCSS 설정
└── package.json               # NPM 패키지 설정
```

### Nord 테마 색상 시스템

#### Polar Night (어두운 배경)
- `nord-0` (#2e3440): 가장 어두운 배경
- `nord-1` (#3b4252): 밝은 배경, 상태 표시줄
- `nord-2` (#434c5e): UI 요소 배경
- `nord-3` (#4c566a): 주석, 비활성 텍스트

#### Snow Storm (밝은 텍스트/배경)
- `nord-4` (#d8dee9): 기본 텍스트
- `nord-5` (#e5e9f0): 밝은 텍스트
- `nord-6` (#eceff4): 가장 밝은 텍스트/배경

#### Frost (강조, UI 컴포넌트)
- `nord-7` (#8fbcbb): 보조 강조
- `nord-8` (#88c0d0): 주요 강조, 링크
- `nord-9` (#81a1c1): 키워드, 태그
- `nord-10` (#5e81ac): 함수, 메서드

#### Aurora (상태, 알림)
- `nord-11` (#bf616a): 에러, 삭제
- `nord-12` (#d08770): 경고
- `nord-13` (#ebcb8b): 주의, 문자열
- `nord-14` (#a3be8c): 성공, 추가
- `nord-15` (#b48ead): 특수 강조

### DaisyUI 테마 매핑

프로젝트의 DaisyUI 테마는 Nord 색상에 매핑되어 있습니다:

```javascript
{
  'primary': '#88c0d0',      // nord8
  'secondary': '#81a1c1',    // nord9
  'accent': '#8fbcbb',       // nord7
  'neutral': '#4c566a',      // nord3
  'base-100': '#2e3440',     // nord0
  'info': '#5e81ac',         // nord10
  'success': '#a3be8c',      // nord14
  'warning': '#ebcb8b',      // nord13
  'error': '#bf616a',        // nord11
}
```

### 사용 예시

#### Tailwind CSS 클래스 사용

```html
<!-- Nord 색상 직접 사용 -->
<div class="bg-nord-0 text-nord-6">
  <h1 class="text-nord-8">제목</h1>
  <p class="text-nord-4">본문 텍스트</p>
</div>

<!-- DaisyUI 컴포넌트 사용 -->
<button class="btn btn-primary">Primary Button</button>
<button class="btn btn-secondary">Secondary Button</button>
<button class="btn btn-error">Delete</button>
```

#### 커스텀 클래스 사용

`src/input.css`에 정의된 커스텀 클래스:

```html
<!-- 카드 -->
<div class="card-nord p-6">
  <h3>카드 제목</h3>
  <p>카드 내용</p>
</div>

<!-- 기능 카드 (hover 효과 포함) -->
<div class="feature-card">
  <div class="icon-container">
    <!-- 아이콘 -->
  </div>
  <h3>기능 제목</h3>
  <p>기능 설명</p>
</div>

<!-- Nord 그라디언트 텍스트 -->
<h1 class="text-gradient-nord">멋진 제목</h1>

<!-- Nord 링크 -->
<a href="#" class="link-nord">링크</a>

<!-- Nord 배지 -->
<span class="badge-nord">New</span>
```

### 새로운 뷰 파일 작성하기

새로운 PHP 뷰 파일을 작성할 때 다음 템플릿을 사용하세요:

```php
<!DOCTYPE html>
<html lang="ko" data-theme="nord">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>페이지 제목 - CI4 CMS</title>
    <link href="/assets/css/output.css" rel="stylesheet">
</head>
<body class="min-h-screen bg-nord-0 text-nord-6">

    <!-- 네비게이션 -->
    <nav class="navbar bg-nord-1 shadow-nord">
        <!-- 네비게이션 내용 -->
    </nav>

    <!-- 메인 콘텐츠 -->
    <main class="container mx-auto px-4 py-8">
        <!-- 콘텐츠 -->
    </main>

    <!-- 푸터 -->
    <footer class="footer footer-center p-10 bg-nord-1 text-nord-4">
        <!-- 푸터 내용 -->
    </footer>

</body>
</html>
```

### 디자인 원칙

#### 1. 일관성
- 모든 페이지에서 Nord 색상 팔레트를 사용
- DaisyUI 컴포넌트의 일관된 스타일 유지
- 간격(spacing)과 타이포그래피 일관성 유지

#### 2. 접근성
- 적절한 색상 대비 (WCAG AA 기준 4.5:1 이상)
- 키보드 네비게이션 지원
- 스크린 리더 호환성

#### 3. 반응형 디자인
- 모바일 우선(mobile-first) 접근
- Tailwind의 반응형 접두사 활용 (sm:, md:, lg:, xl:)

#### 4. 성능
- CSS는 minify되어 배포
- 불필요한 클래스 제거 (PurgeCSS 자동 적용)

### 컴포넌트 가이드

#### 버튼

```html
<!-- Primary -->
<button class="btn btn-primary">Primary</button>

<!-- Secondary -->
<button class="btn btn-secondary">Secondary</button>

<!-- Error -->
<button class="btn btn-error">Delete</button>

<!-- Outline -->
<button class="btn btn-primary btn-outline">Outline</button>

<!-- Sizes -->
<button class="btn btn-sm">Small</button>
<button class="btn">Normal</button>
<button class="btn btn-lg">Large</button>
```

#### 카드

```html
<!-- DaisyUI Card -->
<div class="card bg-nord-1 shadow-xl">
  <div class="card-body">
    <h2 class="card-title text-nord-6">Card Title</h2>
    <p class="text-nord-4">Card content goes here.</p>
    <div class="card-actions justify-end">
      <button class="btn btn-primary">Action</button>
    </div>
  </div>
</div>

<!-- Custom Nord Card -->
<div class="card-nord p-6">
  <h3 class="text-2xl font-bold text-nord-6 mb-4">Title</h3>
  <p class="text-nord-4">Content</p>
</div>
```

#### 알림 (Alert)

```html
<!-- Success -->
<div class="alert alert-success">
  <svg><!-- icon --></svg>
  <span>Success message</span>
</div>

<!-- Error -->
<div class="alert alert-error">
  <svg><!-- icon --></svg>
  <span>Error message</span>
</div>

<!-- Warning -->
<div class="alert alert-warning">
  <svg><!-- icon --></svg>
  <span>Warning message</span>
</div>
```

#### 폼 요소

```html
<!-- Input -->
<input type="text"
       placeholder="Type here"
       class="input input-bordered w-full" />

<!-- Textarea -->
<textarea class="textarea textarea-bordered w-full"
          placeholder="Bio"></textarea>

<!-- Select -->
<select class="select select-bordered w-full">
  <option disabled selected>Pick one</option>
  <option>Option 1</option>
  <option>Option 2</option>
</select>

<!-- Checkbox -->
<input type="checkbox" class="checkbox" />

<!-- Toggle -->
<input type="checkbox" class="toggle" />
```

### 개발 팁

#### 1. Watch 모드 사용
개발 중에는 항상 `npm run dev`를 실행하여 CSS 변경사항을 자동으로 반영하세요.

#### 2. 커스텀 스타일 추가
커스텀 스타일이 필요한 경우 `src/input.css`에 추가하세요:

```css
@layer components {
  .my-custom-class {
    @apply bg-nord-1 text-nord-6 p-4 rounded-lg;
  }
}
```

#### 3. Tailwind IntelliSense
VS Code를 사용한다면 "Tailwind CSS IntelliSense" 확장 프로그램을 설치하여 자동완성을 활용하세요.

#### 4. DaisyUI 컴포넌트 탐색
더 많은 DaisyUI 컴포넌트는 공식 문서를 참고하세요: https://daisyui.com/components/

### 문제 해결

#### CSS가 적용되지 않을 때

1. CSS 빌드 확인:
```bash
npm run build
```

2. 빌드된 파일 확인:
```bash
ls -lh public/assets/css/output.css
```

3. 브라우저 캐시 삭제 (Ctrl/Cmd + Shift + R)

#### Tailwind 클래스가 작동하지 않을 때

1. `tailwind.config.js`의 `content` 경로 확인
2. 새로운 뷰 파일이 content 경로에 포함되어 있는지 확인
3. CSS 재빌드

## API 문서

본 프로젝트는 RESTful API를 제공하며, OpenAPI 3.0 스펙으로 문서화되어 있습니다.

### 📚 API 문서 보기

**[📖 인터랙티브 API 문서 (Redoc)](http://localhost:8080/docs/api-docs.html)**

개발 서버 실행 후 브라우저에서 `http://localhost:8080/docs/api-docs.html`로 접속하여 인터랙티브한 API 문서를 확인할 수 있습니다.

### API 개요

- **Base URL**: `http://localhost:8080/api/v1`
- **인증 방식**: Bearer Token (Shield 기반)
- **포맷**: JSON
- **버전**: v1

### 주요 API 카테고리

| 카테고리 | 엔드포인트 수 | 설명 |
|---------|------------|------|
| **Authentication** | 5 | 사용자 등록, 로그인, 토큰 관리 |
| **Posts** | 10 | 포스트 CRUD, 검색, 필터링 |
| **Categories** | 6 | 카테고리 관리 |
| **Tags** | 5 | 태그 관리 |
| **Comments** | 7 | 댓글 작성, 수정, 모더레이션 |
| **Media** | 5 | 파일 업로드, 다운로드, 관리 |
| **Users** | 7 | 사용자 및 권한 관리 |
| **Tenants** | 7 | 멀티테넌시 관리 |

### 빠른 시작

#### 1. 로그인

```bash
curl -X POST http://localhost:8080/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123"
  }'
```

#### 2. 토큰을 사용하여 API 호출

```bash
curl -X GET http://localhost:8080/api/v1/posts \
  -H "Authorization: Bearer {your_token}"
```

### OpenAPI 스펙 파일

OpenAPI 스펙 파일은 `public/docs/openapi.yaml`에 있으며, 웹에서 [`http://localhost:8080/docs/openapi.yaml`](http://localhost:8080/docs/openapi.yaml)로 접근할 수 있습니다.

이 파일을 사용하여:
- Postman, Insomnia 등에서 컬렉션 생성
- 클라이언트 SDK 자동 생성
- API 테스트 자동화

## 라이선스

MIT License - 자세한 내용은 [LICENSE](LICENSE) 파일을 참고하세요.

## 참고

- [CodeIgniter 4 공식 문서](https://codeigniter.com/user_guide/)
- [Shield 패키지](https://shield.codeigniter.com/)
- [DaisyUI](https://daisyui.com/)
- [Tailwind CSS](https://tailwindcss.com/)
