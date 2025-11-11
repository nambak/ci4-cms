<?php

namespace Tests\Api;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Comments API Tests
 *
 * OpenAPI 스펙 기반 댓글 API 테스트
 * 참조: docs/openapi.yaml - Comments endpoints
 */
class CommentsApiTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected $token;

    /**
     * @test
     * GET /api/v1/comments
     * 댓글 목록 조회 (인증 불필요)
     */
    public function test_get_comments_list(): void
    {
        $result = $this->get('/api/v1/comments');

        $result->assertStatus(200);
        $result->assertJSONFragment(['status' => 'success']);

        $json = $result->getJSON();
        $this->assertObjectHasProperty('data', $json);
        $this->assertIsArray($json->data);
    }

    /**
     * @test
     * GET /api/v1/comments?post_id=1
     * 특정 포스트의 댓글 조회
     */
    public function test_get_comments_filtered_by_post(): void
    {
        $result = $this->get('/api/v1/comments?post_id=1');

        $result->assertStatus(200);
        $json = $result->getJSON();

        // 모든 댓글이 post_id = 1 이어야 함
        foreach ($json->data as $comment) {
            $this->assertEquals(1, $comment->post_id);
        }
    }

    /**
     * @test
     * POST /api/v1/comments
     * 댓글 작성 (인증 필요)
     */
    public function test_create_comment_with_auth(): void
    {
        $this->loginAsUser();

        $commentData = [
            'post_id' => 1,
            'content' => '테스트 댓글입니다.'
        ];

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->withBodyFormat('json')
            ->post('/api/v1/comments', $commentData);

        $result->assertStatus(201);
        $json = $result->getJSON();
        $this->assertEquals($commentData['content'], $json->data->content);
    }

    /**
     * @test
     * POST /api/v1/comments
     * 인증 없이 댓글 작성 실패
     */
    public function test_create_comment_fails_without_auth(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/comments', [
                'post_id' => 1,
                'content' => '댓글'
            ]);

        $result->assertStatus(401);
    }

    /**
     * @test
     * GET /api/v1/comments/{id}
     * 댓글 상세 조회
     */
    public function test_get_comment_by_id(): void
    {
        $result = $this->get('/api/v1/comments/1');

        $result->assertStatus(200);
        $json = $result->getJSON();
        $this->assertObjectHasProperty('data', $json);
        $this->assertEquals(1, $json->data->id);
    }

    /**
     * @test
     * PUT /api/v1/comments/{id}
     * 댓글 수정 (작성자 또는 Admin)
     */
    public function test_update_own_comment(): void
    {
        $this->loginAsUser();
        $commentId = $this->createTestComment();

        $updateData = [
            'content' => '수정된 댓글 내용'
        ];

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->withBodyFormat('json')
            ->put("/api/v1/comments/{$commentId}", $updateData);

        $result->assertStatus(200);
        $json = $result->getJSON();
        $this->assertEquals($updateData['content'], $json->data->content);
    }

    /**
     * @test
     * DELETE /api/v1/comments/{id}
     * 댓글 삭제 (작성자 또는 Admin)
     */
    public function test_delete_own_comment(): void
    {
        $this->loginAsUser();
        $commentId = $this->createTestComment();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->delete("/api/v1/comments/{$commentId}");

        $result->assertStatus(200);
    }

    /**
     * @test
     * POST /api/v1/comments/{id}/replies
     * 대댓글 작성
     */
    public function test_create_reply_to_comment(): void
    {
        $this->loginAsUser();

        $replyData = [
            'content' => '대댓글입니다.'
        ];

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->withBodyFormat('json')
            ->post('/api/v1/comments/1/replies', $replyData);

        $result->assertStatus(201);
        $json = $result->getJSON();
        $this->assertEquals(1, $json->data->parent_id);
        $this->assertEquals($replyData['content'], $json->data->content);
    }

    /**
     * @test
     * POST /api/v1/comments/{id}/moderate
     * 댓글 모더레이션 (Moderator 이상 권한 필요)
     */
    public function test_moderate_comment_with_moderator_role(): void
    {
        $this->loginAsModerator();

        $moderateData = [
            'status' => 'approved'
        ];

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->withBodyFormat('json')
            ->post('/api/v1/comments/1/moderate', $moderateData);

        $result->assertStatus(200);
    }

    /**
     * @test
     * POST /api/v1/comments/{id}/moderate
     * 권한 없이 모더레이션 실패
     */
    public function test_moderate_comment_fails_without_permission(): void
    {
        $this->loginAsUser();

        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->withBodyFormat('json')
            ->post('/api/v1/comments/1/moderate', [
                'status' => 'approved'
            ]);

        $result->assertStatus(403);
    }

    // Helper Methods

    protected function loginAsUser(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/login', [
                'email' => 'user@example.com',
                'password' => 'password123'
            ]);

        $json = $result->getJSON();
        $this->token = $json->data->token ?? null;
    }

    protected function loginAsModerator(): void
    {
        $result = $this->withBodyFormat('json')
            ->post('/api/v1/auth/login', [
                'email' => 'moderator@example.com',
                'password' => 'password123'
            ]);

        $json = $result->getJSON();
        $this->token = $json->data->token ?? null;
    }

    protected function createTestComment(): int
    {
        $result = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->withBodyFormat('json')
            ->post('/api/v1/comments', [
                'post_id' => 1,
                'content' => '테스트 댓글'
            ]);

        $json = $result->getJSON();
        return $json->data->id ?? 1;
    }
}
