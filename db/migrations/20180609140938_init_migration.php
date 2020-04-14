<?php


use Phinx\Migration\AbstractMigration;

class InitMigration extends AbstractMigration
{
    public function change() {
        // users
        $table = $this->table('users')
                ->addColumn('username', 'text')
                ->addColumn('password', 'text')
                ->addColumn('created_at', 'timestamp')
                ->addColumn('updated_at', 'timestamp', ['null' => true])
                ->addColumn('deleted_at', 'timestamp', ['null' => true])
                ->create();
    }
}
