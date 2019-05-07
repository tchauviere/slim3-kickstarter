<?php


use Phinx\Seed\AbstractSeed;

class Users extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $appSettings = include __DIR__.'/../../src/config/settings.php';

        $data = [
            [
                'firstname'    => 'John',
                'lastname' => 'Doe',
                'email' => 'john@doe.local',
                'password' => sha1($appSettings['settings']['secret'].'123456789'),
                'role_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        $users = $this->table('users');
        $users->insert($data)
            ->save();
    }
}
