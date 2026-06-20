<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultPagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'slug' => 'homepage',
                'title' => 'Home',
                // Hero, features and portals are rendered from the settings
                // JSON (with template defaults) — no content HTML needed.
                'content' => '',
                'is_enabled' => true,
                'is_public' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'about',
                'title' => 'About Us',
                // The page template provides the layout; content is plain prose.
                'content' => '<p>Our school is committed to providing a nurturing environment where students can grow academically, socially, and personally.</p>
                    <h2>Our Mission</h2>
                    <p>To provide quality education that empowers students to become responsible citizens and lifelong learners, equipped with the knowledge and skills needed to succeed in an ever-changing world.</p>
                    <h2>Our Vision</h2>
                    <p>To be a leading educational institution that fosters innovation, creativity, and excellence while maintaining the highest standards of academic integrity and moral values.</p>
                    <h2>Our Values</h2>
                    <ul>
                        <li><strong>Excellence:</strong> We strive for the highest standards in everything we do</li>
                        <li><strong>Integrity:</strong> We act with honesty and moral principles</li>
                        <li><strong>Respect:</strong> We value diversity and treat everyone with dignity</li>
                        <li><strong>Innovation:</strong> We embrace new ideas and creative thinking</li>
                        <li><strong>Community:</strong> We foster a sense of belonging and collaboration</li>
                    </ul>',
                'is_enabled' => true,
                'is_public' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact Us',
                // Contact details come from site settings; the template
                // renders the info cards, form and map.
                'content' => '',
                'is_enabled' => true,
                'is_public' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'student-portal',
                'title' => 'Student Portal',
                'content' => '<div class="max-w-md mx-auto py-16">
                    <div class="bg-white p-8 rounded-lg shadow-md">
                        <h1 class="text-2xl font-bold text-center mb-6">Student Portal</h1>
                        <p class="text-center text-gray-600 mb-6">Access your grades, assignments, and school information</p>
                        <div class="text-center">
                            <a href="/login?role=student" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg inline-block">Login to Student Portal</a>
                        </div>
                    </div>
                </div>',
                'is_enabled' => true,
                'is_public' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('pages')->insertOrIgnore($pages);

        // Insert default site settings
        $settings = [
            ['key' => 'site_name', 'value' => 'Our School', 'type' => 'text', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Excellence in Education', 'type' => 'text', 'group' => 'general'],
            ['key' => 'contact_email', 'value' => 'info@school.com', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact_phone', 'value' => '+1 (555) 123-4567', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'contact_address', 'value' => '123 School Street, Education City', 'type' => 'text', 'group' => 'contact'],
            ['key' => 'primary_color', 'value' => '#4F6BED', 'type' => 'text', 'group' => 'appearance'],
            ['key' => 'secondary_color', 'value' => '#3D55C8', 'type' => 'text', 'group' => 'appearance'],
            ['key' => 'logo_url', 'value' => '', 'type' => 'file', 'group' => 'appearance'],
            ['key' => 'enable_registration', 'value' => 'false', 'type' => 'boolean', 'group' => 'general'],
        ];

        foreach ($settings as $setting) {
            $setting['created_at'] = now();
            $setting['updated_at'] = now();
        }

        DB::table('site_settings')->insertOrIgnore($settings);
    }
}
