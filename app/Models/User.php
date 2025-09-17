<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use \Illuminate\Database\Eloquent\Relations\HasOne;
use \Illuminate\Database\Eloquent\Relations\HasMany;
use \Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'deleted_at' => 'datetime',
        'status' => 'boolean',
    ];

    /**
     * Определяет связь многие ко многим с моделью Role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles(): BelongsToMany {
        // Указываем таблицу промежуточной связи ('role_user'),
        // имя внешнего ключа для пользователя ('user_id')
        // имя внешнего ключа для роли ('role_id')
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * Проверяет, есть ли у пользователя указанная роль.
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole($roleName): bool {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Проверка нескольких ролей
     *
     * @param array $roleNames
     * @return bool
     */
    public function hasRoles(array $roleNames): bool {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    /**
     * Определяет связь "один к одному" с моделью UserData.
     */
    public function userData(): HasOne {
        return $this->hasOne(UserData::class);
    }

    /**
     * Определяет связь "один ко многим" с моделью Category.
     */
    public function categories(): HasMany {
        return $this->hasMany(Category::class, 'manager_id');
    }

    public function reLogin(): HasOne {
        return $this->hasOne(ReLogin::class, 'from_user_id')
            ->orWhere('to_user_id', $this->id);
    }

    public function companyPlanner(): HasOne {
        return $this->hasOne(CompanyPlanner::class);
    }

    // Accessor для получения объединённых данных юзера
    public function getMergedUserDataAttribute(): array {
        $userData = $this->userData ? $this->userData->toArray() : [];
        unset($userData['user_id'], $userData['id']);

        // Добавляем company_categories из модели UserData
        $userData['company_categories'] = $this->userData->company_categories ?? [];

        // 1 Объединяем данные пользователя и очищенные данные user_data
        $mergedUser = array_merge($this->toArray(), $userData);

        // 2 Проверяем наличие ролей и выбираем первый элемент
        if ($this->roles->isNotEmpty()) {
            $mergedUser['role'] = $this->roles->first()->name;
        }
        else {
            // Если ролей нет, устанавливаем значение null
            $mergedUser['role'] = null;
        }

        // 3 Получаем категории - могут быть только у менеджера, к которому привязаны категории
        $categories = $this->categories()->get()->map(function ($category) {
            // Оставляем поля категории
            return [
                'id' => $category->id,
                'name' => $category->name,
                'required' => $category->required,
                'groups' => $category->groups,
            ];
        })->toArray();

        // 4 Получаем данные о ReLogin
        $mergedUser['re_login'] = $this->reLogin;

        $mergedUser['categories'] = $categories;

        // Создает развернутый обьект company_planner для user роли
        $companyPlanner = $this->companyPlanner;
        if ($companyPlanner) {
            $surfacesData = collect($companyPlanner->surfaces)->map(function ($surface) {
                // Разделяем строку на ID поверхности и amount
                [$surfaceId, $amount] = explode('_', $surface);

                // Находим объект Surface по ID
                $surfaceModel = Surface::find($surfaceId);

                // Если объект найден, возвращаем структуру с объектом и amount
                if ($surfaceModel) {
                    return [
                        'surface' => [
                            'id' => $surfaceModel->id,
                            'name' => $surfaceModel->name,
                            'divided_bool' => $surfaceModel->divided_bool,
                        ],
                        'amount' => (int) $amount,
                    ];
                }

                return null;
            })->filter()->values()->toArray();

            $mergedUser['company_planner'] = $surfacesData;
        } else {
            $mergedUser['company_planner'] = [];
        }

        // Удаляем поле roles и user_data из результата
        unset($mergedUser['roles']);
        // Удаляем поле user_data
        unset($mergedUser['user_data']);

        return $mergedUser;
    }
}
