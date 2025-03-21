<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\RolesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Gate;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => RolesEnum::class,
        ];
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_members')
            ->using(ProjectMember::class)
            ->withPivot('role', 'status', 'approved_at')
            ->as('membership')
            ->withTimestamps();
    }

    public function permissions(Request $request)
    {
        $policies = Gate::policies();
        $permissions = collect();
        $routeModels = collect($request->route()->parameters())->mapWithKeys(function ($model) {
            return [get_class($model) => $model];
        });

        foreach ($policies as $modelClass => $policyClass) {
            $policy = Gate::resolvePolicy($policyClass);
            $model = $routeModels->get($modelClass) ?? $modelClass;

            $abilities = collect((new \ReflectionClass($policy))->getMethods())
                ->filter(function ($method) {
                    return $method->isPublic() && !$method->isStatic() && $method->name !== '__construct';
                });

            foreach ($abilities as $ability) {
                $params = $ability->getParameters();

                if (count($params) === 2 && is_string($model)) {
                    continue;
                }

                if (Gate::check($ability->name, $model)) {
                    $permissions->push(strtolower(class_basename($model) . ':' . $ability->name));
                }
            }
        }

        return $permissions->unique()->values()->all();
    }
}
