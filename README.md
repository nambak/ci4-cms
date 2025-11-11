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

# 환경 설정
cp env .env

# 데이터베이스 설정 (.env 파일에서)
# DB_HOST, DB_NAME, DB_USER, DB_PASS 설정

# 마이그레이션 실행
php spark migrate

# 시드 데이터 로드 (선택)
php spark db:seed SampleSeeder
```

### 실행

```bash
# 개발 서버 실행
php spark serve

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
