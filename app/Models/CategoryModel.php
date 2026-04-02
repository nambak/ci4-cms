<?php

namespace App\Models;

use App\Entities\CategoryEntity;
use CodeIgniter\Model;
use Faker\Generator;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = CategoryEntity::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'tenant_id', 'name',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'tenant_id' => 'required|integer',
        'name'      => 'required|max_length[255]',
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * fake Category 모델 인스턴스 반환
     *
     * @param Generator $faker
     * @return CategoryEntity
     */
    public function fake(Generator $faker): CategoryEntity
    {
        return new CategoryEntity([
            'tenant_id' => 1,
            'name'      => $faker->word,
        ]);
    }
}
