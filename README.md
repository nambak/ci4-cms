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

```plaintext
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

## 프론트엔드

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

## 라이선스

MIT License - 자세한 내용은 [LICENSE](LICENSE) 파일을 참고하세요.

## 참고

- [CodeIgniter 4 공식 문서](https://codeigniter.com/user_guide/)
- [Shield 패키지](https://shield.codeigniter.com/)
- [DaisyUI](https://daisyui.com/)
- [Tailwind CSS](https://tailwindcss.com/)
