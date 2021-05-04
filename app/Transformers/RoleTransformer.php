<?php

namespace App\Transformers;


use App\Models\Role;
use League\Fractal\TransformerAbstract;

class RoleTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Role $role)
    {
        return [
            'userRoleId' => (int) $role->id,
            'userRole' => (string) $role->role,
            'creationDate' => (string) $role->created_at,
            'lastChange' => (string) $role->updated_at,
            'deletedDate' => isset($role->deleted_at) ? (string) $role->deleted_at : null,
        ];
    }
}
