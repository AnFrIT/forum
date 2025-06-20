<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // General settings
            ['key' => 'forum_name', 'value' => 'AL-INSAF', 'type' => 'string', 'group' => 'general', 'description' => 'Название форума'],
            ['key' => 'forum_description', 'value' => 'Исламский форум', 'type' => 'text', 'group' => 'general', 'description' => 'Описание форума'],
            ['key' => 'forum_logo', 'value' => null, 'type' => 'string', 'group' => 'general', 'description' => 'Логотип форума'],
            ['key' => 'forum_keywords', 'value' => 'ислам, форум, обсуждение, акъида, фикх, тафсир, хадис', 'type' => 'text', 'group' => 'general', 'description' => 'Ключевые слова для SEO'],

            // Registration settings
            ['key' => 'registration_enabled', 'value' => 'true', 'type' => 'boolean', 'group' => 'registration', 'description' => 'Разрешить регистрацию новых пользователей'],
            ['key' => 'email_verification_required', 'value' => 'true', 'type' => 'boolean', 'group' => 'registration', 'description' => 'Требовать подтверждение email'],
            ['key' => 'manual_user_approval', 'value' => 'false', 'type' => 'boolean', 'group' => 'registration', 'description' => 'Требуется одобрение администратора для новых пользователей'],

            // Posting settings
            ['key' => 'posts_per_page', 'value' => '20', 'type' => 'integer', 'group' => 'posting', 'description' => 'Количество сообщений на странице'],
            ['key' => 'topics_per_page', 'value' => '25', 'type' => 'integer', 'group' => 'posting', 'description' => 'Количество тем на странице'],
            ['key' => 'max_attachment_size', 'value' => '10', 'type' => 'integer', 'group' => 'posting', 'description' => 'Максимальный размер вложения в МБ'],
            ['key' => 'allowed_file_types', 'value' => 'jpg,jpeg,png,gif,pdf,doc,docx,zip,rar', 'type' => 'text', 'group' => 'posting', 'description' => 'Разрешенные типы файлов'],
            ['key' => 'require_post_approval', 'value' => 'false', 'type' => 'boolean', 'group' => 'posting', 'description' => 'Требовать одобрение сообщений'],

            // Email settings
            ['key' => 'email_notifications', 'value' => 'true', 'type' => 'boolean', 'group' => 'email', 'description' => 'Включить email уведомления'],
            ['key' => 'admin_email', 'value' => 'admin@forum.local', 'type' => 'string', 'group' => 'email', 'description' => 'Email администратора'],

            // Theme settings
            ['key' => 'theme_color', 'value' => '#0099CC', 'type' => 'string', 'group' => 'theme', 'description' => 'Основной цвет темы'],
            ['key' => 'enable_dark_mode', 'value' => 'true', 'type' => 'boolean', 'group' => 'theme', 'description' => 'Включить темную тему'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
