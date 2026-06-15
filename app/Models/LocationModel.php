<?php

namespace App\Models;

use CodeIgniter\Model;

class LocationModel extends Model
{
    protected $table = 'locations';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'helper_id',
        'latitude',
        'longitude',
        'updated_at'
    ];

    protected $useSoftDeletes = false;

    public function getHelper()
    {
        return $this->belongsTo('UserModel', 'helper_id');
    }

    public function updateLocation(int $helperId, float $latitude, float $longitude): bool
    {
        $data = [
            'helper_id'  => $helperId,
            'latitude'   => $latitude,
            'longitude'  => $longitude,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $existing = $this->where('helper_id', $helperId)->first();

        if ($existing) {
            return $this->update($existing['id'], $data);
        }

        return $this->insert($data);
    }

    public function getLocationByHelper(int $helperId): ?array
    {
        return $this->where('helper_id', $helperId)->first();
    }
}
