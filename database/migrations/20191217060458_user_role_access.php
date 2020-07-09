<?php

declare(strict_types=1);

use think\migration\Migrator;

/**
 * 用户与角色中间表.
 */
class UserRoleAccess extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('user_role_access', ['id' => false, 'engine' => 'InnoDB', 'collation' => config('database.connections.mysql.charset') . '_unicode_ci', 'primary_key' => ['user_id', 'role_id']]);
        $table->addColumn('user_id', 'integer', ['limit' => 11, 'comment' => '用户主键'])
            ->addColumn('role_id', 'integer', ['limit' => 11, 'comment' => '角色主键'])
            ->create();
    }
}
