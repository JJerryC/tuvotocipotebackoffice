<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

use Illuminate\Database\Eloquent\Events\ModelCreated;
use Illuminate\Database\Eloquent\Events\ModelUpdated;
use Illuminate\Database\Eloquent\Events\ModelDeleted;
use Illuminate\Database\Eloquent\Events\ModelRestored;

use App\Models\SystemLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(
            [ModelCreated::class, ModelUpdated::class, ModelDeleted::class, ModelRestored::class],
            function ($event) {
                $model = $event->getModel();
                $action = strtolower(class_basename($event)); // e.g., 'modelupdated' → 'updated'

                $changes = null;
                if ($event instanceof ModelUpdated) {
                    $changes = collect($model->getChanges())
                        ->mapWithKeys(fn($v, $k) => [
                            $k => "{$model->getOriginal($k)} → {$v}"
                        ]);
                }

                SystemLog::create([
                    'model_type' => get_class($model),
                    'model_id'   => $model->getKey(),
                    'table_name' => $model->getTable(),
                    'action'     => $action,
                    'changes'    => $changes,
                    'user_id'    => optional(Auth::user())->id,
                    'user_name'  => optional(Auth::user())->name,
                    'module'     => 'global-observer',
                    'ip_address' => Request::ip(),
                    'user_agent' => substr(Request::userAgent() ?? '', 0, 255),
                ]);
            }
        );
    }
}
