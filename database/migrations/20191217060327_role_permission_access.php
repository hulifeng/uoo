<?php

declare(strict_types=1);

use think\migration\Migrator;

/**
 * 角色与规则中间表.
 */
class RolePermissionAccess extends Migrator
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
        $table = $this->table('role_permission_access', ['id' => false, 'engine' => 'InnoDB', 'collation' => config('database.connections.mysql.charset') . '_unicode_ci', 'primary_key' => ['role_id', 'permission_id']]);
        $table->addColumn('role_id', 'integer', ['limit' => 11, 'comment' => '角色主键'])
            ->addColumn('permission_id', 'integer', ['limit' => 11, 'comment' => '规则主键'])
            ->create();
    }
}
