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
                'title' => 'Welcome to Our School',
                'content' => '<div class="text-center py-16">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">Welcome to Our School</h1>
                    <p class="text-xl text-gray-600 mb-8">Excellence in Education, Building Tomorrow\'s Leaders</p>
                    <div class="grid md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-2">Quality Education</h3>
                            <p class="text-gray-600">We provide world-class education with experienced faculty and modern facilities.</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-2">Student Success</h3>
                            <p class="text-gray-600">Our students achieve excellence in academics, sports, and personal development.</p>
                        </div>
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-2">Modern Facilities</h3>
                            <p class="text-gray-600">State-of-the-art classrooms, laboratories, library, and sports facilities.</p>
                        </div>
                    </div>
                </div>',
                'is_enabled' => true,
                'is_public' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'about',
                'title' => 'About Us',
                'content' => '<div class="max-w-4xl mx-auto py-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-6">About Our School</h1>
                    <div class="prose prose-lg">
                        <p>Our school has been a cornerstone of educational excellence for over [X] years. We are committed to providing a nurturing environment where students can grow academically, socially, and personally.</p>
                        
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
                        </ul>
                    </div>
                </div>',
                'is_enabled' => true,
                'is_public' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact Us',
                'content' => '<div class="max-w-4xl mx-auto py-8">
                    <h1 class="text-3xl font-bold text-gray-900 mb-6">Contact Us</h1>
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h2 class="text-xl font-semibold mb-4">Get in Touch</h2>
                            <p class="text-gray-600 mb-6">We would love to hear from you. Send us a message and we\'ll respond as soon as possible.</p>
                            
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                                    </svg>
                                    <span>info@school.com</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span>123 School Street, Education City</span>
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                                    </svg>
                                    <span>+1 (555) 123-4567</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            {{-- Contact form will be handled by PublicController --}}
                            <div id="contact-form-placeholder" class="bg-gray-50 p-6 rounded-lg">
                                <p class="text-center text-gray-500">Contact form will be loaded here</p>
                            </div>
                        </div>
                    </div>
                </div>',
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
            ['key' => 'primary_color', 'value' => '#3B82F6', 'type' => 'text', 'group' => 'appearance'],
            ['key' => 'secondary_color', 'value' => '#1E40AF', 'type' => 'text', 'group' => 'appearance'],
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
