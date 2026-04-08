<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use CodeIgniter\Model;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * API V1 공통 기반 컨트롤러
 *
 * 모든 API 컨트롤러가 상속받는 추상 클래스.
 * JSON 응답 포맷을 기본으로 설정합니다.
 */
abstract class BaseApiController extends ResourceController
{
    protected $format = 'json';

    protected $codes = [
        'created'            => 201,
        'deleted'            => 204,
        'updated'            => 200,
        'no_content'         => 204,
        'invalid_data'       => 422,
        'resource_not_found' => 404,
        'server_error'       => 500,
        'forbidden'          => 403,
    ];

    /**
     * user를 찾고, 존재하지 않으면 예외 처리.
     *
     * @param int|string $id
     * @return User
     */
    protected function findUserOrFail(int|string $id): User
    {
        $user = auth()->getProvider()->findById($id);

        if (!$user) {
            throw new PageNotFoundException('User not found');
        }

        return $user;
    }

    /**
     * 표준 응답 메서드
     *
     */
    protected function responseWith(array $data, $pagination = null): ResponseInterface
    {
        $response = [
            'status' => 'success',
            'code'   => 200,
            'data'   => [
                'items' => $data,
            ]
        ];

        if ($pagination) {
            $response['data']['pagination'] = [
                'current_page' => $pagination->getCurrentPage(),
                'total'        => $pagination->getTotal(),
                'per_page'     => $pagination->getPerPage(),
                'last_page'    => $pagination->getLastPage(),
            ];
        }

        return $this->respond($response);
    }

    protected function responseWithItem(array $data, int $statusCode = 200): ResponseInterface
    {
        return $this->respond([
            'status' => 'success',
            'code'   => $statusCode,
            'data'   => $data,
        ], $statusCode);
    }

    protected function responseWithMessage(string $message): ResponseInterface
    {
        return $this->respond([
            'status'  => 'success',
            'code'    => 200,
            'message' => $message,
        ]);
    }

    protected function failValidationErrors(mixed  $errors, ?string $code = null, string $message = ''): ResponseInterface
    {
        return $this->respond([
            'status'  => 'error',
            'code'    => $this->codes['invalid_data'],
            'message' => is_array($errors) ? (array_values($errors)[0] ?? 'Invalid data') : $errors,
            'errors'  => $errors,
        ], 422);
    }

    protected function failNotFound(string $description = 'Not Found', ?string $code = null, string $message = ''): ResponseInterface
    {
        return $this->respond([
            'status'  => 'error',
            'code'    => $this->codes['resource_not_found'],
            'message' => $description,
        ], 404);
    }

    protected function failOnModelError(): ResponseInterface
    {
        if (empty($this->model->errors())) {
            log_message('error', $this->model->db->error()['message']);

            if ($this->model->db->error()['code'] == 1062) {
                return $this->failValidationErrors('Duplicate entry');
            }

            return $this->failServerError('Database error');
        } else {
            return $this->failValidationErrors($this->model->errors());
        }
    }
}
