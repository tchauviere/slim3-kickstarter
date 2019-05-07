<?php


use Phinx\Seed\AbstractSeed;

class Roles extends AbstractSeed
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
        $data = [
            [
                'name'    => 'admin',
                'description' => 'Users with admin rights',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],[
                'name'    => 'user',
                'description' => 'Simple user',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ];

        $roles = $this->table('roles');
        $roles->insert($data)
            ->save();
    }
}
