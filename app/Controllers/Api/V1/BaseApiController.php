<?php

declare(strict_types=1);

namespace App\Controllers\Api\V1;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Shield\Entities\User;

/**
 * API V1 공통 기반 컨트롤러
 *
 * 모든 API 컨트롤러가 상속받는 추상 클래스.
 * JSON 응답 포맷을 기본으로 설정합니다.
 */
abstract class BaseApiController extends ResourceController
{
    protected $format = 'json';


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
    protected function responseWith($data, $pagination = null)
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
}
