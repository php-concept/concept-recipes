<?php declare(strict_types=1);

namespace Database\Seeders;

use Concept\Extensions\DatabaseEloquent\Contracts\SeederInterface;
use Illuminate\Database\Capsule\Manager as CapsuleManager;
use Illuminate\Database\Seeder;

final class ContactSubmissionsSeeder extends Seeder implements SeederInterface
{
    public function run(): void
    {
        $table = CapsuleManager::schema()->getConnection()->table('contact_submissions');
        $table->truncate();

        $table->insert([
            [
                'name' => 'Demo User',
                'email' => 'demo@example.com',
                'message' => 'Seeded contact submission for the recipes demo.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Anna',
                'email' => 'anna@example.com',
                'message' => 'Hello from the database seeder.',
                'created_at' => date('Y-m-d H:i:s'),
            ],
        ]);
    }
}
