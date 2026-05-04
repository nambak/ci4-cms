<?php

namespace App\Models;

use App\Entities\CommentEntity;
use App\Enums\CommentState;
use CodeIgniter\Model;
use CodeIgniter\Test\Fabricator;
use Faker\Generator;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = CommentEntity::class;
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = ['post_id', 'user_id', 'parent_id', 'content', 'state'];


    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'post_id'   => 'required|integer|is_natural_no_zero',
        'user_id'   => 'required',
        'parent_id' => 'permit_empty|integer|is_natural_no_zero',
        'content'   => 'required|min_length[1]',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function __construct()
    {
        parent::__construct();

        $this->validationRules['state'] = 'required|in_list['
            . implode(',', array_column(CommentState::cases(), 'value'))
            . ']';
    }

    public function fake(Generator &$faker): array
    {
        $post = (new Fabricator(PostModel::class))->create();
        $user = (new Fabricator(UserModel::class))->create();

        return [
            'post_id'   => $post->id,
            'user_id'   => $user->id,
            'parent_id' => null,
            'content'   => $faker->paragraph,
            'state'     => $faker->randomElement(CommentState::cases())->value,
        ];
    }
}
