<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->pluck('value', 'key');

        $systemStats = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_size' => 'N/A',
            'storage_used' => 'N/A',
        ];

        return view('admin.settings.index', compact('settings', 'systemStats'));
    }

    public function update(Request $request)
    {
        $tab = $request->input('tab', 'general');

        // Обработка разных вкладок
        switch ($tab) {
            case 'general':
                $this->updateGeneralSettings($request);
                break;
            case 'appearance':
                $this->updateAppearanceSettings($request);
                break;
            case 'users':
                $this->updateUserSettings($request);
                break;
            case 'content':
                $this->updateContentSettings($request);
                break;
            case 'security':
                $this->updateSecuritySettings($request);
                break;
        }

        return back()->with('success', 'Настройки сохранены!');
    }

    private function updateGeneralSettings($request)
    {
        $settings = [
            'site_name' => $request->input('site_name'),
            'site_description' => $request->input('site_description'),
            'site_url' => $request->input('site_url'),
            'timezone' => $request->input('timezone'),
            'default_language' => $request->input('default_language'),
            'site_status' => $request->input('site_status'),
            'maintenance_message' => $request->input('maintenance_message'),
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
        }
    }

    private function updateAppearanceSettings($request)
    {
        $settings = [
            'color_scheme' => $request->input('color_scheme'),
            'theme' => $request->input('theme'),
            'custom_css' => $request->input('custom_css'),
            'custom_js' => $request->input('custom_js'),
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
        }
    }

    private function updateUserSettings($request)
    {
        $settings = [
            'allow_registration' => $request->has('allow_registration') ? '1' : '0',
            'approve_new_users' => $request->has('approve_new_users') ? '1' : '0',
            'min_password_length' => $request->input('min_password_length'),
            'max_posts_per_day' => $request->input('max_posts_per_day'),
            'max_topics_per_day' => $request->input('max_topics_per_day'),
            'session_timeout' => $request->input('session_timeout'),
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
        }
    }

    private function updateContentSettings($request)
    {
        $settings = [
            'max_post_length' => $request->input('max_post_length'),
            'max_attachment_size' => $request->input('max_attachment_size'),
            'posts_per_page' => $request->input('posts_per_page'),
            'topics_per_page' => $request->input('topics_per_page'),
            'auto_approve_posts' => $request->has('auto_approve_posts') ? '1' : '0',
            'enable_post_editing' => $request->has('enable_post_editing') ? '1' : '0',
            'enable_attachments' => $request->has('enable_attachments') ? '1' : '0',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
        }
    }

    private function updateSecuritySettings($request)
    {
        $settings = [
            'captcha_enabled' => $request->has('captcha_enabled') ? '1' : '0',
            'max_login_attempts' => $request->input('max_login_attempts'),
            'auto_backup_enabled' => $request->has('auto_backup_enabled') ? '1' : '0',
            'backup_frequency' => $request->input('backup_frequency'),
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value ?? '']);
        }
    }

    public function create()
    {
        return view('admin.settings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:settings,key',
            'value' => 'nullable|string',
            'type' => 'required|in:string,text,integer,float,boolean,json',
            'group' => 'required|string',
            'description' => 'nullable|string',
        ]);

        Setting::create($validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Настройка успешно создана!');
    }

    public function destroy($key)
    {
        Setting::where('key', $key)->delete();

        Cache::forget('settings');

        return back()->with('success', 'Настройка удалена!');
    }

    public function export()
    {
        $settings = Setting::all()->pluck('value', 'key');

        return response()->json($settings)
            ->header('Content-Disposition', 'attachment; filename="forum_settings.json"');
    }

    public function import(Request $request)
    {
        $request->validate([
            'settings_file' => 'required|file|mimes:json',
        ]);

        try {
            $content = file_get_contents($request->file('settings_file')->path());
            $settings = json_decode($content, true);

            foreach ($settings as $key => $value) {
                Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
