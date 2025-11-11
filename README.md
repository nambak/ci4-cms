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

## API 문서

### API 구조

본 프로젝트는 RESTful API를 제공하며, 버전별로 관리됩니다.

**Base URL**: `http://localhost:8080/api/v1`

**인증 방식**: Bearer Token (Shield 기반)

### 주요 API 엔드포인트

#### 1. 인증 (Authentication)

| Method | Endpoint | 설명 | 인증 필요 |
|--------|----------|------|-----------|
| POST | `/api/v1/auth/register` | 사용자 등록 | ❌ |
| POST | `/api/v1/auth/login` | 로그인 | ❌ |
| POST | `/api/v1/auth/logout` | 로그아웃 | ✅ |
| GET | `/api/v1/auth/me` | 현재 사용자 정보 | ✅ |
| POST | `/api/v1/auth/refresh` | 토큰 갱신 | ✅ |

#### 2. 포스트 (Posts)

| Method | Endpoint | 설명 | 인증 필요 |
|--------|----------|------|-----------|
| GET | `/api/v1/posts` | 포스트 목록 (페이지네이션) | ❌ |
| POST | `/api/v1/posts` | 포스트 생성 | ✅ (Editor+) |
| GET | `/api/v1/posts/{id}` | 포스트 상세 | ❌ |
| PUT | `/api/v1/posts/{id}` | 포스트 수정 | ✅ (Editor+) |
| DELETE | `/api/v1/posts/{id}` | 포스트 삭제 | ✅ (Admin+) |
| GET | `/api/v1/posts/{id}/comments` | 포스트 댓글 목록 | ❌ |
| POST | `/api/v1/posts/{id}/publish` | 포스트 발행 | ✅ (Editor+) |
| GET | `/api/v1/posts/category/{slug}` | 카테고리별 포스트 | ❌ |
| GET | `/api/v1/posts/tag/{slug}` | 태그별 포스트 | ❌ |
| GET | `/api/v1/posts/search` | 포스트 검색 | ❌ |

#### 3. 카테고리 (Categories)

| Method | Endpoint | 설명 | 인증 필요 |
|--------|----------|------|-----------|
| GET | `/api/v1/categories` | 카테고리 목록 | ❌ |
| POST | `/api/v1/categories` | 카테고리 생성 | ✅ (Admin) |
| GET | `/api/v1/categories/{id}` | 카테고리 상세 | ❌ |
| PUT | `/api/v1/categories/{id}` | 카테고리 수정 | ✅ (Admin) |
| DELETE | `/api/v1/categories/{id}` | 카테고리 삭제 | ✅ (Admin) |
| GET | `/api/v1/categories/{id}/posts` | 카테고리 내 포스트 | ❌ |

#### 4. 태그 (Tags)

| Method | Endpoint | 설명 | 인증 필요 |
|--------|----------|------|-----------|
| GET | `/api/v1/tags` | 태그 목록 | ❌ |
| POST | `/api/v1/tags` | 태그 생성 | ✅ (Admin) |
| GET | `/api/v1/tags/{id}` | 태그 상세 | ❌ |
| PUT | `/api/v1/tags/{id}` | 태그 수정 | ✅ (Admin) |
| DELETE | `/api/v1/tags/{id}` | 태그 삭제 | ✅ (Admin) |

#### 5. 댓글 (Comments)

| Method | Endpoint | 설명 | 인증 필요 |
|--------|----------|------|-----------|
| GET | `/api/v1/comments` | 댓글 목록 | ❌ |
| POST | `/api/v1/comments` | 댓글 작성 | ✅ |
| GET | `/api/v1/comments/{id}` | 댓글 상세 | ❌ |
| PUT | `/api/v1/comments/{id}` | 댓글 수정 | ✅ (작성자/Admin) |
| DELETE | `/api/v1/comments/{id}` | 댓글 삭제 | ✅ (작성자/Admin) |
| POST | `/api/v1/comments/{id}/replies` | 대댓글 작성 | ✅ |
| POST | `/api/v1/comments/{id}/moderate` | 댓글 모더레이션 | ✅ (Moderator+) |

#### 6. 미디어 (Media)

| Method | Endpoint | 설명 | 인증 필요 |
|--------|----------|------|-----------|
| GET | `/api/v1/media` | 미디어 목록 | ✅ |
| POST | `/api/v1/media/upload` | 파일 업로드 | ✅ |
| GET | `/api/v1/media/{id}` | 미디어 상세 | ✅ |
| DELETE | `/api/v1/media/{id}` | 미디어 삭제 | ✅ (Admin+) |
| GET | `/api/v1/media/{id}/download` | 파일 다운로드 | ✅ |

#### 7. 사용자 (Users)

| Method | Endpoint | 설명 | 인증 필요 |
|--------|----------|------|-----------|
| GET | `/api/v1/users` | 사용자 목록 | ✅ (Admin) |
| POST | `/api/v1/users` | 사용자 생성 | ✅ (Admin) |
| GET | `/api/v1/users/{id}` | 사용자 상세 | ✅ |
| PUT | `/api/v1/users/{id}` | 사용자 수정 | ✅ |
| DELETE | `/api/v1/users/{id}` | 사용자 삭제 | ✅ (Admin) |
| GET | `/api/v1/users/{id}/permissions` | 사용자 권한 조회 | ✅ |
| GET | `/api/v1/users/{id}/roles` | 사용자 역할 조회 | ✅ |

#### 8. 테넌트 (Tenants)

| Method | Endpoint | 설명 | 인증 필요 |
|--------|----------|------|-----------|
| GET | `/api/v1/tenants` | 테넌트 목록 | ✅ (Super Admin) |
| POST | `/api/v1/tenants` | 테넌트 생성 | ✅ (Super Admin) |
| GET | `/api/v1/tenants/{id}` | 테넌트 상세 | ✅ (Owner/Admin) |
| PUT | `/api/v1/tenants/{id}` | 테넌트 수정 | ✅ (Owner) |
| DELETE | `/api/v1/tenants/{id}` | 테넌트 삭제 | ✅ (Super Admin) |
| GET | `/api/v1/tenants/{id}/settings` | 테넌트 설정 조회 | ✅ (Owner/Admin) |
| PUT | `/api/v1/tenants/{id}/settings` | 테넌트 설정 수정 | ✅ (Owner) |

### API 요청/응답 예시

#### 로그인 요청

```bash
POST /api/v1/auth/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password123"
}
```

#### 성공 응답

```json
{
  "status": "success",
  "code": 200,
  "message": "로그인 성공",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": {
      "id": 1,
      "email": "user@example.com",
      "name": "홍길동",
      "role": "editor"
    }
  }
}
```

#### 포스트 생성 요청

```bash
POST /api/v1/posts
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGc...
Content-Type: application/json

{
  "title": "새로운 포스트",
  "slug": "new-post",
  "content": "포스트 내용...",
  "category_id": 1,
  "tags": [1, 2, 3],
  "status": "draft"
}
```

#### 에러 응답

```json
{
  "status": "error",
  "code": 401,
  "message": "인증이 필요합니다",
  "errors": []
}
```

### API 인증

API 요청 시 Bearer 토큰을 헤더에 포함해야 합니다:

```bash
Authorization: Bearer {your_token_here}
```

### 페이지네이션

목록 조회 API는 페이지네이션을 지원합니다:

```bash
GET /api/v1/posts?page=1&per_page=10&sort=created_at&order=desc
```

### 필터링 & 검색

```bash
# 카테고리 필터
GET /api/v1/posts?category_id=1

# 검색
GET /api/v1/posts/search?q=keyword

# 상태 필터
GET /api/v1/posts?status=published
```

## 라이선스

MIT License - 자세한 내용은 [LICENSE](LICENSE) 파일을 참고하세요.

## 참고

- [CodeIgniter 4 공식 문서](https://codeigniter.com/user_guide/)
- [Shield 패키지](https://shield.codeigniter.com/)
- [DaisyUI](https://daisyui.com/)
- [Tailwind CSS](https://tailwindcss.com/)
