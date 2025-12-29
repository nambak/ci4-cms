# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## 프로젝트 개요

CodeIgniter 4 기반의 멀티테넌시 CMS 플랫폼입니다. URL 기반 테넌트 분리, Shield RBAC 권한 관리를 특징으로 합니다.

## 핵심 기술 스택

- **Framework**: CodeIgniter 4 (PHP 8.1+)
- **인증 & 권한**: Shield (CodeIgniter 공식 RBAC)
- **테스트**: PHPUnit 10.5
- **API 문서**: OpenAPI 3.0 스펙

## 주요 개발 명령어

### 서버 실행
```bash
php spark serve
```

### 데이터베이스
```bash
# 마이그레이션 실행
php spark migrate

# 마이그레이션 롤백
php spark migrate:rollback

# 시드 데이터 로드
php spark db:seed SampleSeeder
```

### 테스트
```bash
# 전체 테스트 실행
composer test
# 또는
vendor/bin/phpunit

# 특정 테스트 파일만 실행
vendor/bin/phpunit tests/api/AuthenticationApiTest.php

# 코드 커버리지 포함
vendor/bin/phpunit --coverage-html build/coverage
```

### 의존성 관리
```bash
# PHP 패키지 설치
composer install

# 오토로드 재생성
composer dump-autoload
```

## 아키텍처

### 멀티테넌시 구조
- URL 기반 테넌트 분리: `yoursite.com/tenant-slug`
- 각 테넌트는 독립적인 콘텐츠와 권한을 가짐
- 테넌트 필터를 통한 요청 라우팅 및 데이터 격리

### 인증 & 권한
- CodeIgniter Shield를 사용한 사용자 인증
- RBAC(Role-Based Access Control) 기반 권한 관리
- Bearer Token 기반 API 인증
- Shield의 기본 라우트는 `app/Config/Routes.php:10`에서 자동 등록됨

### API 구조
- RESTful API 설계
- Base URL: `/api/v1`
- OpenAPI 3.0 스펙: `public/docs/openapi.yaml`
- 인터랙티브 문서: `http://localhost:8080/docs/api-docs.html`

### 디렉토리 구조
```
app/
├── Controllers/     # 컨트롤러
├── Models/          # 모델 (현재 비어있음)
├── Views/           # 뷰
├── Filters/         # 필터 (인증, 테넌트)
├── Database/
│   ├── Migrations/  # 데이터베이스 마이그레이션
│   └── Seeds/       # 시드 데이터
└── Config/          # 설정 파일

tests/
├── api/             # API 통합 테스트
├── unit/            # 단위 테스트
└── database/        # 데이터베이스 테스트

public/
└── docs/            # API 문서 (openapi.yaml, api-docs.html)
```

## 테스트 작성 가이드

### API 테스트
- OpenAPI 스펙 기반으로 테스트 작성
- 테스트 파일 위치: `tests/api/`
- 기존 테스트 예시:
  - `AuthenticationApiTest.php` (5개 테스트)
  - `PostsApiTest.php` (15개 테스트)
  - `CategoriesApiTest.php` (7개 테스트)
  - `CommentsApiTest.php` (9개 테스트)

### 테스트 데이터베이스 설정
`phpunit.xml.dist` 파일의 54-61번 줄에서 테스트 DB 설정을 언커멘트하고 수정하세요:
```xml
<env name="database.tests.hostname" value="localhost"/>
<env name="database.tests.database" value="ci4_test"/>
<env name="database.tests.username" value="root"/>
<env name="database.tests.password" value=""/>
```

## 환경 설정

### .env 파일
- `env` 파일을 `.env`로 복사하여 사용
- 주요 설정 항목:
  - 데이터베이스 연결 정보 (DB_HOST, DB_NAME, DB_USER, DB_PASS)
  - 환경 모드 (CI_ENVIRONMENT: development/production)

## 중요 사항

### CodeIgniter 4 컨벤션
- 클래스명은 PascalCase, 파일명도 동일
- 네임스페이스는 PSR-4를 따름: `App\`, `Config\`
- 컨트롤러는 `BaseController`를 상속
- 모델은 `CodeIgniter\Model`을 상속

### Shield 인증
- Shield의 기본 라우트(`/login`, `/register` 등)는 자동으로 등록됨
- API 인증은 Bearer Token 사용
- 권한은 그룹(Group)과 퍼미션(Permission) 기반

### API 문서 관리
- OpenAPI 스펙을 수정하면 API 변경사항을 문서에 반영
- 새로운 엔드포인트 추가 시 `public/docs/openapi.yaml` 업데이트
- 테스트도 함께 작성하여 스펙과 구현의 일관성 유지

## 디자인 가이드

### 색상 팔레트
프로젝트의 CSS에서 색상을 사용할 때는 [Nord Theme](https://www.nordtheme.com/docs/colors-and-palettes) 색상을 사용합니다.

#### Polar Night (어두운 배경)
| 변수 | 색상코드 | 용도 |
|------|---------|------|
| nord0 | `#2e3440` | 가장 어두운 배경 |
| nord1 | `#3b4252` | 밝은 배경, 상태 표시줄 |
| nord2 | `#434c5e` | UI 요소 배경 |
| nord3 | `#4c566a` | 주석, 비활성 텍스트 |

#### Snow Storm (밝은 텍스트/배경)
| 변수 | 색상코드 | 용도 |
|------|---------|------|
| nord4 | `#d8dee9` | 기본 텍스트 |
| nord5 | `#e5e9f0` | 밝은 텍스트 |
| nord6 | `#eceff4` | 가장 밝은 텍스트/배경 |

#### Frost (강조, UI 컴포넌트)
| 변수 | 색상코드 | 용도 |
|------|---------|------|
| nord7 | `#8fbcbb` | 보조 강조 |
| nord8 | `#88c0d0` | 주요 강조, 링크 |
| nord9 | `#81a1c1` | 키워드, 태그 |
| nord10 | `#5e81ac` | 함수, 메서드 |

#### Aurora (상태, 알림)
| 변수 | 색상코드 | 용도 |
|------|---------|------|
| nord11 | `#bf616a` | 에러, 삭제 |
| nord12 | `#d08770` | 경고 |
| nord13 | `#ebcb8b` | 주의, 문자열 |
| nord14 | `#a3be8c` | 성공, 추가 |
| nord15 | `#b48ead` | 특수 강조 |

### 개발 로드맵
현재 Phase 1(기본 구조) 진행 중:
- 프로젝트 초기 설정 ✓
- 데이터베이스 설계 및 마이그레이션 (진행 중)
- 테넌트 관리 기능 (예정)
- 사용자 & 권한 관리 (예정)
