<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder {
    /**
     * Заполняет таблицу пользователей начальными данными.
     */
    public function run(): void {
        $arrayGroup = config('site.users.array-group');
        $arrayRoles = config('site.users.roles');
        // Получаем роли
        // Получаем все уникальные роли из списка пользователей
        $roles = Role::whereIn('name', $arrayRoles)
            ->get()->keyBy('name');

        // Список пользователей
        $users = [
            // Администратор
            [
                'email' => 'admin@admin.com',
                'password' => 'password1',
                'name' => 'Admin User',
                'surname' => 'User Surname',
                'name_invoice_recipient' => 'Admin Recipient',
                'email_invoice_recipient' => 'admininvoice@admin.com',
                'company_name' => 'Admin Company',
                'company_number' => '999999999',
                'c_o' => 'Admin c/o',
                'post_address' => 'Admin Street 1',
                'postcode' => '111111',
                'phone' => '+3801234567-1',
                'phone_2' => '+3801234567-2',
                'municipality_number' => '2222',
                'kommune' => 'Admin Kommune',
                'country' => 'Admin Country',
                'number_country' => '12',
                'group' => $arrayGroup[0],
                'reference_number' => '88888888',
                'status' => 1,
                'role' => 'admin',
            ],
            // Дизайнер 1
            [
                'name' => 'Designer User 1',
                'surname' => 'User Surname',
                'email' => 'designer1@designer.com',
                'password' => 'password1',
                'company_name' => 'Designer Company 1',
                'phone' => '+3801234567-3',
                'status' => 1,
                'role' => 'designer',
                'name_invoice_recipient' => 'Designer Recipient 1',
                'company_number' => '999999997',
                'email_invoice_recipient' => 'designerinvoice1@designer.com',
                'reference_number' => '88888890',
                'c_o' => 'Designer 1 c/o',
                'post_address' => 'Designer Street 1',
                'postcode' => '111113',
                'phone_2' => '+3801234567-4',
                'municipality_number' => '2224',
                'kommune' => 'Designer Kommune 1',
                'country' => 'Designer Country 1',
                'number_country' => '14',
                'group' => $arrayGroup[0],
            ],
            // Дизайнер 2
            [
                'name' => 'Designer User 2',
                'surname' => 'User Surname',
                'email' => 'designer2@designer.com',
                'password' => 'password1',
                'company_name' => 'Designer Company 2',
                'phone' => '+3801234567-5',
                'status' => 1,
                'role' => 'designer',
                'name_invoice_recipient' => 'Designer Recipient 2',
                'company_number' => '999999996',
                'email_invoice_recipient' => 'designerinvoice2@designer.com',
                'reference_number' => '88888891',
                'c_o' => 'Designer 2 c/o',
                'post_address' => 'Designer Street 2',
                'postcode' => '111114',
                'phone_2' => '+3801234567-6',
                'municipality_number' => '2225',
                'kommune' => 'Designer Kommune 2',
                'country' => 'Designer Country 2',
                'number_country' => '15',
                'group' => $arrayGroup[0],
            ],
            // Менеджер 1
            [
                'name' => 'CM User 1',
                'surname' => 'User Surname',
                'email' => 'cm1@cm.com',
                'password' => 'password1',
                'company_name' => 'CM Company 1',
                'phone' => '+3801234567-7',
                'status' => 1,
                'role' => 'cm',
                'name_invoice_recipient' => 'CM Recipient 1',
                'company_number' => '999999995',
                'email_invoice_recipient' => 'cminvoice1@cm.com',
                'reference_number' => '88888892',
                'c_o' => 'CM 1 c/o',
                'post_address' => 'CM Street 1',
                'postcode' => '111115',
                'phone_2' => '+3801234567-8',
                'municipality_number' => '2226',
                'kommune' => 'CM Kommune 1',
                'country' => 'CM Country 1',
                'number_country' => '16',
                'group' => $arrayGroup[0],
            ],
            // Менеджер 2
            [
                'name' => 'CM User 2',
                'surname' => 'User Surname',
                'email' => 'cm2@cm.com',
                'password' => 'password1',
                'company_name' => 'CM Company 2',
                'phone' => '+3801234567-9',
                'status' => 1,
                'role' => 'cm',
                'name_invoice_recipient' => 'CM Recipient 2',
                'company_number' => '999999994',
                'email_invoice_recipient' => 'cminvoice2@cm.com',
                'reference_number' => '88888893',
                'c_o' => 'CM 2 c/o',
                'post_address' => 'CM Street 2',
                'postcode' => '111116',
                'phone_2' => '+3801234567-10',
                'municipality_number' => '2227',
                'kommune' => 'CM Kommune 2',
                'country' => 'CM Country 2',
                'number_country' => '17',
                'group' => $arrayGroup[0],
            ],
            // Regular User
            [
                'name' => 'Regular User',
                'surname' => 'User Surname',
                'email' => 'user@user.com',
                'password' => 'password1',
                'company_name' => 'User Company',
                'phone' => '+3801234567-17',
                'status' => 1,
                'role' => 'user',
                'name_invoice_recipient' => 'User Recipient',
                'company_number' => '999999990',
                'email_invoice_recipient' => 'userinvoice@user.com',
                'reference_number' => '88888897',
                'c_o' => 'User c/o',
                'post_address' => 'User Street 1',
                'postcode' => '111120',
                'phone_2' => '+3801234567-18',
                'municipality_number' => '2231',
                'kommune' => 'User Kommune',
                'country' => 'User Country',
                'number_country' => '21',
                'group' => $arrayGroup[0],
                'category_ids' => [1],
            ],
            // Regular User 2
            [
                'name' => 'Regular User 2',
                'surname' => 'User Surname',
                'email' => 'user2@user.com',
                'password' => 'password1',
                'company_name' => 'User Company 2',
                'phone' => '+3801234567-23',
                'status' => 1,
                'role' => 'user',
                'name_invoice_recipient' => 'User Recipient 2',
                'company_number' => '999999989',
                'email_invoice_recipient' => 'userinvoice2@user.com',
                'reference_number' => '88888898',
                'c_o' => 'User 2 c/o',
                'post_address' => 'User Street 2',
                'postcode' => '111121',
                'phone_2' => '+3801234567-24',
                'municipality_number' => '2232',
                'kommune' => 'User Kommune 2',
                'country' => 'User Country 2',
                'number_country' => '22',
                'group' => $arrayGroup[0],
                'category_ids' => [1],
            ],
            // Regular User 3
            [
                'name' => 'Regular User 3',
                'surname' => 'User Surname',
                'email' => 'user3@user.com',
                'password' => 'password1',
                'company_name' => 'User Company 3',
                'phone' => '+3801234567-25',
                'status' => 1,
                'role' => 'user',
                'name_invoice_recipient' => 'User Recipient 3',
                'company_number' => '999999988',
                'email_invoice_recipient' => 'userinvoice3@user.com',
                'reference_number' => '88888899',
                'c_o' => 'User 3 c/o',
                'post_address' => 'User Street 3',
                'postcode' => '111122',
                'phone_2' => '+3801234567-26',
                'municipality_number' => '2233',
                'kommune' => 'User Kommune 3',
                'country' => 'User Country 3',
                'number_country' => '23',
                'group' => $arrayGroup[0],
                'category_ids' => [1],
            ],
            // CM-Admin
            [
                'name' => 'CM Admin User',
                'surname' => 'User Surname',
                'email' => 'cmadmin@cmadmin.com',
                'password' => 'password1',
                'company_name' => 'CM Admin Company',
                'phone' => '+3801234567-11',
                'status' => 1,
                'role' => 'cm-admin',
                'name_invoice_recipient' => 'CM Admin Recipient',
                'company_number' => '999999993',
                'email_invoice_recipient' => 'cmadmininvoice@cmadmin.com',
                'reference_number' => '88888894',
                'c_o' => 'CM Admin c/o',
                'post_address' => 'CM Admin Street 1',
                'postcode' => '111117',
                'phone_2' => '+3801234567-12',
                'municipality_number' => '2228',
                'kommune' => 'CM Admin Kommune',
                'country' => 'CM Admin Country',
                'number_country' => '18',
                'group' => $arrayGroup[0],
            ],
            // Принтер
            [
                'name' => 'Printer User',
                'surname' => 'User Surname',
                'email' => 'printer@printer.com',
                'password' => 'password1',
                'company_name' => 'Printer Company',
                'phone' => '+3801234567-13',
                'status' => 1,
                'role' => 'printer',
                'name_invoice_recipient' => 'Printer Recipient',
                'company_number' => '999999992',
                'email_invoice_recipient' => 'printerinvoice@printer.com',
                'reference_number' => '88888895',
                'c_o' => 'Printer c/o',
                'post_address' => 'Printer Street 1',
                'postcode' => '111118',
                'phone_2' => '+3801234567-14',
                'municipality_number' => '2229',
                'kommune' => 'Printer Kommune',
                'country' => 'Printer Country',
                'number_country' => '19',
                'group' => $arrayGroup[0],
            ],
            // Принтер 2
            [
                'name' => 'Printer User 2',
                'surname' => 'User Surname',
                'email' => 'printer2@printer.com',
                'password' => 'password1',
                'company_name' => 'Printer Company 2',
                'phone' => '+3801234567-14',
                'status' => 1,
                'role' => 'printer',
                'name_invoice_recipient' => 'Printer Recipient',
                'company_number' => '999999992',
                'email_invoice_recipient' => 'printerinvoice@printer.com',
                'reference_number' => '88888895',
                'c_o' => 'Printer c/o',
                'post_address' => 'Printer Street 2',
                'postcode' => '111118',
                'phone_2' => '+3801234567-14',
                'municipality_number' => '2229',
                'kommune' => 'Printer Kommune',
                'country' => 'Printer Country',
                'number_country' => '19',
                'group' => $arrayGroup[0],
            ],
            // Дистрибьютор
            [
                'name' => 'Distributor User',
                'surname' => 'User Surname',
                'email' => 'distributor@distributor.com',
                'password' => 'password1',
                'company_name' => 'Distributor Company',
                'phone' => '+3801234567-15',
                'status' => 1,
                'role' => 'distributor',
                'name_invoice_recipient' => 'Distributor Recipient',
                'company_number' => '999999991',
                'email_invoice_recipient' => 'distributorinvoice@distributor.com',
                'reference_number' => '88888896',
                'c_o' => 'Distributor c/o',
                'post_address' => 'Distributor Street 1',
                'postcode' => '111119',
                'phone_2' => '+3801234567-16',
                'municipality_number' => '2230',
                'kommune' => 'Distributor Kommune',
                'country' => 'Distributor Country',
                'number_country' => '20',
                'group' => $arrayGroup[0],
            ],
        ];

        // Добавляем пользователей и назначаем им роли
        foreach ($users as $userData) {
            // Данные для таблицы users
            $userDataForUser = [
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
                'status' => $userData['status'],
                'email_verified_at' => Carbon::now(),
            ];

            // 1 Создаем пользователя
            $user = User::create($userDataForUser);

            // 2 Назначаем пользователю роль
            $user->roles()->attach($roles[$userData['role']]->id);

            // исключает поля таблицы user
            $userDataForUserData = array_merge(
                ['user_id' => $user->id],
                array_filter($userData, function ($key) {
                    return !in_array($key, ['name', 'email', 'password', 'role', 'status']);
                }, ARRAY_FILTER_USE_KEY)
            );

            // 3 Создаем запись в user_data
            UserData::create($userDataForUserData);
        }
    }
}
