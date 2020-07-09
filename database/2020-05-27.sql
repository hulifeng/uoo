-- 数据库日志表
CREATE TABLE IF NOT EXISTS `db_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '模型名',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '访问地址',
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '操作',
  `sql` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'sql',
  `user_id` int(11) NOT NULL COMMENT '操作员id',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL,
  `delete_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 部门表
CREATE TABLE IF NOT EXISTS `dept` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '部门名称',
  `pid` int(11) NOT NULL COMMENT '上级部门',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `dept` (`id`, `name`, `pid`, `status`, `create_time`, `update_time`, `delete_time`) VALUES
	(1, 'UooLu', 0, 1, 1590510869, 1590510869, 0);


-- 登录日志表
CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '用户标识',
  `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '操作',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '访问地址',
  `ip` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '访问ip',
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '访问者user_agnet',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 权限表
CREATE TABLE IF NOT EXISTS `permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '规则名称',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '名称',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父级标识',
  `type` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '类别',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `path` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'path',
  `redirect` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'redirect',
  `component` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'component',
  `icon` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'icon',
  `permission` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'permission',
  `keepAlive` int(11) NOT NULL DEFAULT '0' COMMENT 'keepAlive',
  `hidden` int(11) NOT NULL DEFAULT '0' COMMENT 'hidden',
  `hideChildrenInMenu` int(11) NOT NULL DEFAULT '0' COMMENT 'hideChildrenInMenu',
  `action_type` int(11) NOT NULL DEFAULT '0',
  `button_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO `permission` (`id`, `name`, `title`, `pid`, `type`, `status`, `path`, `redirect`, `component`, `icon`, `permission`, `keepAlive`, `hidden`, `hideChildrenInMenu`, `action_type`, `button_type`, `create_time`, `update_time`, `delete_time`) VALUES
	(1, 'Index', '首页', 0, 'path', 1, '/', '/dashboard/workplace', 'BasicLayout', '', '', 0, 0, 0, 0, NULL, 0, 1593875048, 0),
	(2, 'Dashboard', '仪表盘', 1, 'path', 1, '/dashboard', '/dashboard/workplace', 'RouteView', 'dashboard', 'Analysis,Workspace', 0, 0, 0, 0, NULL, 0, 0, 0),
	(3, 'Analysis', '分析页', 2, 'menu', 1, '/dashboard/analysis', '', 'Analysis', '', 'Analysis', 0, 0, 0, 0, NULL, 0, 0, 0),
	(4, 'InfoAnalysis', '详情', 3, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Info', 0, 1593876265, 0),
	(7, 'System', '系统管理', 1, 'path', 1, '/system', '/system/permission', 'PageView', 'slack', 'Permission,Role,Account,Dept', 0, 0, 0, 0, NULL, 0, 0, 0),
	(8, 'Permission', '菜单管理', 7, 'menu', 1, '/system/permission', '', 'Permission', '', 'Permission', 0, 0, 0, 0, NULL, 0, 0, 0),
	(9, 'FetchPermission', '列表', 8, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Fetch', 0, 1593876286, 0),
	(10, 'CreatePermission', '新增', 8, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Create', 0, 1593876297, 0),
	(11, 'UpdatePermission', '修改', 8, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Update', 0, 1593876310, 0),
	(12, 'DeletePermission', '删除', 8, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Delete', 0, 1593876563, 0),
	(13, 'Role', '角色管理', 7, 'menu', 1, '/system/role', '', 'Role', '', 'Role', 0, 0, 0, 0, NULL, 0, 0, 0),
	(14, 'FetchRole', '列表', 13, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Fetch', 0, 1593876522, 0),
	(15, 'CreateRole', '新增', 13, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Create', 0, 1593876533, 0),
	(16, 'UpdateRole', '修改', 13, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Update', 0, 1593876552, 0),
	(17, 'DeleteRole', '删除', 13, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Delete', 0, 1593930465, 0),
	(18, 'AccountManager', '管理员管理', 7, 'path', 1, '/system/user', '/system/user/list', 'RouteView', '', '', 0, 0, 1, 0, NULL, 0, 1593927984, 0),
	(19, 'Account', '管理员列表', 18, 'menu', 1, '/system/user/list', '', 'Account', '', 'Account', 0, 0, 0, 0, NULL, 0, 1593930375, 0),
	(20, 'FetchAccount', '列表', 19, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Fetch', 0, 1593930200, 0),
	(21, 'CreateAccount', '新增', 19, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Create', 0, 1593930241, 0),
	(22, 'UpdateAccount', '修改', 19, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Update', 0, 1593930249, 0),
	(23, 'Dept', '部门管理', 7, 'menu', 1, '/system/Dept', '', 'Dept', '', 'Dept', 0, 0, 0, 0, NULL, 0, 0, 0),
	(24, 'FetchDept', '列表', 23, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Fetch', 0, 1593928144, 0),
	(25, 'CreateDept', '新增', 23, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Create', 0, 1593928222, 0),
	(26, 'UpdateDept', '修改', 23, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Update', 0, 1593929061, 0),
	(27, 'DeleteDept', '删除', 23, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Delete', 0, 1593928304, 0),
	(28, 'Post', '岗位管理', 7, 'menu', 1, '/system/post', '', 'Post', '', 'Post', 0, 0, 0, 0, NULL, 0, 0, 0),
	(29, 'FetchPost', '列表', 28, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Fetch', 0, 1593928260, 0),
	(30, 'CreatePost', '新增', 28, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Create', 0, 1593928268, 0),
	(31, 'UpdatePost', '修改', 28, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Update', 0, 1593929051, 0),
	(32, 'DeletePost', '删除', 28, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Delete', 0, 1593928341, 0),
	(33, 'Log', '日志管理', 1, 'path', 1, '/log', '/log/account', 'PageView', 'file-text', 'LogAccount,LogDb', 0, 0, 0, 0, NULL, 0, 0, 0),
	(34, 'LogAccount', '管理员日志', 33, 'menu', 1, '/log/account', '', 'LogAccount', '', 'LogAccount', 0, 0, 0, 0, NULL, 0, 0, 0),
	(35, 'FetchLogAccount', '列表', 34, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Fetch', 0, 1593928379, 0),
	(36, 'DeleteLogAccount', '删除', 34, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Delete', 0, 1593928390, 0),
	(37, 'LogDb', '数据库日志', 33, 'menu', 1, '/log/db', '', 'LogDb', '', 'LogDb', 0, 0, 0, 0, NULL, 0, 0, 0),
	(38, 'FetchLogDb', '列表', 37, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Fetch', 0, 1593928401, 0),
	(39, 'DeleteLogDb', '删除', 37, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Delete', 0, 1593928425, 0),
	(40, 'Profile', '个人页', 1, 'path', 1, '/account', '/account/center', 'RouteView', 'user', 'BaseSettings,SecuritySettings', 0, 0, 0, 0, NULL, 0, 0, 0),
	(41, 'ProfileAccount', '个人中心', 40, 'menu', 1, '/account/center', '', 'Center', '', '', 0, 0, 0, 0, NULL, 0, 0, 0),
	(42, 'ProfileSetting', '个人设置', 40, 'menu', 1, '/account/settings', '/account/settings/base', 'Settings', '', 'BaseSettings,SecuritySettings', 0, 0, 1, 0, NULL, 0, 0, 0),
	(43, 'BaseSettings', '基本设置', 42, 'menu', 1, '/account/settings/base', '', 'BaseSettings', '', 'BaseSettings', 0, 0, 0, 0, NULL, 0, 1593932353, 0),
	(44, 'SaveProfile', '更新信息', 43, 'action', 1, '', '', '', '', '', 0, 0, 0, 2, NULL, 0, 1593928517, 0),
	(45, 'SaveAvatar', '更新头像', 43, 'action', 1, '', '', '', '', '', 0, 0, 0, 2, NULL, 0, 1593928524, 0),
	(46, 'SecuritySettings', '安全设置', 42, 'menu', 1, '/account/settings/security', '', 'SecuritySettings', '', 'BaseSettings', 0, 0, 0, 0, NULL, 1590511221, 1593932359, 0),
	(47, 'UpdateSecurityPassword', '更新密码', 46, 'action', 1, '', '', '', '', '', 0, 0, 0, 2, NULL, 0, 1593928532, 0),
	(48, 'CreateAccountView', '创建用户', 18, 'menu', 1, '/user/create', '', 'AccountForm', '', 'CreateAccountView', 0, 1, 0, 0, NULL, 1590589427, 1593930974, 0),
	(49, 'UpdateAccountView', '更新用户', 18, 'menu', 1, '/user/:id/update', '', 'AccountForm', '', 'UpdateAccountView', 0, 1, 0, 0, NULL, 1590590048, 1593930651, 0),
	(50, 'DeleteAccount', '删除', 19, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Delete', 1593928021, 1593930257, 0),
	(51, 'SaveCreateAccountView', '保存', 48, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Save', 1593928100, 1593928100, 0),
	(52, 'SaveUpdateAccountView', '保存', 49, 'action', 1, '', '', '', '', '', 0, 0, 0, 1, 'Save', 1593928120, 1593928120, 0),
	(53, 'UpdateRoleAccess', '编辑数据权限', 13, 'action', 1, '', '', '', '', '', 0, 0, 0, 2, NULL, 1593929218, 1593929218, 0);

-- 岗位表
CREATE TABLE IF NOT EXISTS `post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '岗位名称',
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '岗位标识',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` int(11) NOT NULL DEFAULT '1' COMMENT '状态',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 角色表
CREATE TABLE IF NOT EXISTS `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '角色唯一标识',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '角色名称',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父级标识',
  `mode` int(11) NOT NULL DEFAULT '3' COMMENT '数据权限类型',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(11) NOT NULL DEFAULT '0',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `role` (`id`, `name`, `title`, `pid`, `mode`, `status`, `create_time`, `update_time`, `delete_time`) VALUES
	(1, 'root', '顶级角色', 0, 0, 1, 0, 0, 0);

-- 角色部门表
CREATE TABLE IF NOT EXISTS `role_dept_access` (
  `role_id` int(11) NOT NULL COMMENT '角色主键',
  `dept_id` int(11) NOT NULL COMMENT '部门主键',
  PRIMARY KEY (`role_id`,`dept_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 角色权限表
CREATE TABLE IF NOT EXISTS `role_permission_access` (
  `role_id` int(11) NOT NULL COMMENT '角色主键',
  `permission_id` int(11) NOT NULL COMMENT '规则主键',
  PRIMARY KEY (`role_id`,`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 管理员表
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '用户唯一标识（登录名）',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '登录密码',
  `hash` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '加密hash',
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `dept_id` int(11) NOT NULL DEFAULT '3' COMMENT '部门标识',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '状态',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '头像',
  `email` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `delete_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user` (`id`, `name`, `password`, `hash`, `nickname`, `dept_id`, `status`, `avatar`, `email`, `create_time`, `update_time`, `delete_time`) VALUES
	(1, 'admin', '$2y$10$NivWBgBTy8f/Sfghr3Bch.38kDb/WL7cncBF7iLG4f8KumkGQeo56', 'US%qMfOqun4', 'Serati Ma', 0, 1, 'storage/topic/avatar.png', 'SeratiMa@aliyun.com', 1589699902, 1589699902, 0);

-- 用户岗位表
CREATE TABLE IF NOT EXISTS `user_post_access` (
  `user_id` int(11) NOT NULL COMMENT '用户主键',
  `post_id` int(11) NOT NULL COMMENT '岗位主键',
  PRIMARY KEY (`user_id`,`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 用户角色表
CREATE TABLE IF NOT EXISTS `user_role_access` (
  `user_id` int(11) NOT NULL COMMENT '用户主键',
  `role_id` int(11) NOT NULL COMMENT '角色主键',
  PRIMARY KEY (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
