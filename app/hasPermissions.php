<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

trait hasPermissions
{
    public function permissions(Request $request)
    {
        $policies = Gate::policies();
        $permissions = collect();
        $routeModels = collect($request->route()->parameters())->mapWithKeys(function ($model) {
            if (is_string($model)) {
                return [];
            }
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
