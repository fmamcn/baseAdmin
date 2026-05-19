
-- -----------------------------
-- Table structure for `ob_ad`
-- -----------------------------
DROP TABLE IF EXISTS `ob_ad`;
CREATE TABLE `ob_ad` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '分类名称',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '图片',
  `sort_order` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='广告';


-- -----------------------------
-- Table structure for `ob_admin`
-- -----------------------------
DROP TABLE IF EXISTS `ob_admin`;
CREATE TABLE `ob_admin` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '管理员用户名',
  `password` varchar(64) NOT NULL DEFAULT '' COMMENT '管理员密码',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0禁用/1启动',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '上次登录时间',
  `last_login_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '上次登录IP',
  `login_count` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '登录次数',
  `create_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='管理员';

-- -----------------------------
-- Records of `ob_admin`
-- -----------------------------
INSERT INTO `ob_admin` VALUES ('1', 'admin', '$2y$10$RIsKnXb9zLz3YCMWpwzONOO/HmmFCNwxLyWFigfxe2jDuS.14fWGK', '1', '1743149338', '222.240.65.94', '58', '0', '1743261329');
INSERT INTO `ob_admin` VALUES ('2', 'demo', '$2y$10$Zj0zkDhgsoK.eIumtqqdOeDAx2itBInebxHCXZVgQ5yfLzTglxQa.', '1', '1545449154', '127.0.0.1', '5', '1539076102', '1743227173');

-- -----------------------------
-- Table structure for `ob_admin_log`
-- -----------------------------
DROP TABLE IF EXISTS `ob_admin_log`;
CREATE TABLE `ob_admin_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` smallint(5) unsigned NOT NULL DEFAULT 0 COMMENT '管理员id',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '管理员用户名',
  `useragent` varchar(255) NOT NULL DEFAULT '' COMMENT 'User-Agent',
  `ip` varchar(16) NOT NULL DEFAULT '' COMMENT 'ip地址',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '请求链接',
  `method` varchar(32) NOT NULL DEFAULT '' COMMENT '请求类型',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '资源类型',
  `param` text NOT NULL COMMENT '请求参数',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '日志备注',
  `create_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='管理员日志';

-- -----------------------------
-- Records of `ob_admin_log`
-- -----------------------------
INSERT INTO `ob_admin_log` VALUES ('1', '1', 'admin', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '222.240.67.144', 'https://e.zztuku.com/admin.php/index/login.html', 'POST', 'html', '{\"username\":\"admin\",\"password\":\"admin888\",\"captcha\":\"rhfj\"}', '登录了后台系统', '1778952615');
INSERT INTO `ob_admin_log` VALUES ('2', '1', 'admin', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '222.240.67.144', 'https://e.zztuku.com/admin.php/index/login.html', 'POST', 'html', '{\"username\":\"admin\",\"password\":\"admin888\",\"captcha\":\"26aw\"}', '登录了后台系统', '1779022006');
INSERT INTO `ob_admin_log` VALUES ('3', '1', 'admin', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '222.240.67.182', 'https://e.zztuku.com/admin.php/index/login.html', 'POST', 'html', '{\"username\":\"admin\",\"password\":\"admin888\",\"captcha\":\"ja8w\"}', '登录了后台系统', '1779108962');
INSERT INTO `ob_admin_log` VALUES ('4', '1', 'admin', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '222.240.67.182', 'https://e.zztuku.com/admin.php/index/login.html', 'POST', 'html', '{\"username\":\"admin\",\"password\":\"admin888\",\"captcha\":\"r388\"}', '登录了后台系统', '1779161169');

-- -----------------------------
-- Table structure for `ob_auth_group`
-- -----------------------------
DROP TABLE IF EXISTS `ob_auth_group`;
CREATE TABLE `ob_auth_group` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `rules` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='权限组';

-- -----------------------------
-- Records of `ob_auth_group`
-- -----------------------------
INSERT INTO `ob_auth_group` VALUES ('1', '超级管理员', '', '1', '6,7,43,44,2,9,28,29,30,3,11,25,26,27,46,47,48,49,50,51,52,4,12,14,19,20,21,13,45,55,5,16,37,38,39,17,40,41,42,15,22,23,24,18,53');

-- -----------------------------
-- Table structure for `ob_auth_group_access`
-- -----------------------------
DROP TABLE IF EXISTS `ob_auth_group_access`;
CREATE TABLE `ob_auth_group_access` (
  `uid` smallint(5) unsigned NOT NULL DEFAULT 0,
  `group_id` smallint(5) unsigned NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限授权';

-- -----------------------------
-- Records of `ob_auth_group_access`
-- -----------------------------
INSERT INTO `ob_auth_group_access` VALUES ('1', '1');
INSERT INTO `ob_auth_group_access` VALUES ('2', '1');

-- -----------------------------
-- Table structure for `ob_auth_rule`
-- -----------------------------
DROP TABLE IF EXISTS `ob_auth_rule`;
CREATE TABLE `ob_auth_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `icon` varchar(64) NOT NULL DEFAULT '',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
  `type` char(4) NOT NULL DEFAULT '' COMMENT 'nav,auth',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 COMMENT='权限规则';

-- -----------------------------
-- Records of `ob_auth_rule`
-- -----------------------------
INSERT INTO `ob_auth_rule` VALUES ('2', '0', '会员', '', 'layui-icon layui-icon-friends', '3', 'nav', '1');
INSERT INTO `ob_auth_rule` VALUES ('3', '0', '扩展', '', 'layui-icon layui-icon-template-1', '4', 'nav', '1');
INSERT INTO `ob_auth_rule` VALUES ('4', '0', '设置', '', 'layui-icon layui-icon-set-fill', '5', 'nav', '1');
INSERT INTO `ob_auth_rule` VALUES ('5', '0', '权限', '', 'layui-icon layui-icon-group', '6', 'nav', '1');
INSERT INTO `ob_auth_rule` VALUES ('6', '0', '控制台', '', 'layui-icon layui-icon-find-fill', '1', 'nav', '1');
INSERT INTO `ob_auth_rule` VALUES ('7', '6', '控制台', 'admin/index/main', '', '0', 'nav', '1');
INSERT INTO `ob_auth_rule` VALUES ('9', '2', '会员管理', 'admin/user/index', '', '0', 'nav', '1');
INSERT INTO `ob_auth_rule` VALUES ('11', '3', '广告管理', 'admin/ad/index', '', '1', 'nav', '1');
INSERT INTO `ob_auth_rule` VALUES ('12', '4', '基本设置', 'admin/config/setting', '', '1', 'nav', '1');
INSERT INTO `ob_auth_rule` VALUES ('13', '4', '系统设置', 'admin/config/system', '', '3', 'nav', '1');
INSERT INTO `ob_auth_rule` VALUES ('14', '4', '设置管理', 'admin/config/index', '', '2', 'nav', '1');
INSERT INTO `ob_auth_rule` VALUES ('15', '5', '权限规则', 'admin/auth/rule', '', '3', 'nav', '1');
INSERT INTO `ob_auth_rule` VALUES ('16', '5', '管理员', 'admin/admin/index', '', '0', 'nav', '1');
INSERT INTO `ob_auth_rule` VALUES ('17', '5', '权限组', 'admin/auth/group', '', '1', 'nav', '1');
INSERT INTO `ob_auth_rule` VALUES ('18', '5', '管理员日志', 'admin/admin/log', '', '5', 'nav', '1');
INSERT INTO `ob_auth_rule` VALUES ('19', '14', '添加', 'admin/config/add', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('20', '14', '编辑', 'admin/config/edit', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('21', '14', '删除', 'admin/config/del', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('22', '15', '添加', 'admin/auth/addRule', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('23', '15', '编辑', 'admin/auth/editRule', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('24', '15', '删除', 'admin/auth/delRule', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('25', '11', '添加', 'admin/ad/add', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('26', '11', '编辑', 'admin/ad/edit', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('27', '11', '删除', 'admin/ad/del', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('28', '9', '添加', 'admin/user/add', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('29', '9', '编辑', 'admin/user/edit', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('30', '9', '删除', 'admin/user/del', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('37', '16', '添加', 'admin/admin/add', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('38', '16', '编辑', 'admin/admin/edit', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('39', '16', '删除', 'admin/admin/del', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('40', '17', '添加', 'admin/auth/addGroup', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('41', '17', '编辑', 'admin/auth/editGroup', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('42', '17', '删除', 'admin/auth/delGroup', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('43', '6', '修改密码', 'admin/index/editPassword', '', '1', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('44', '6', '清除缓存', 'admin/index/clear', '', '2', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('45', '4', '上传设置', 'admin/config/upload', '', '4', 'nav', '1');
INSERT INTO `ob_auth_rule` VALUES ('46', '3', '数据管理', 'admin/database/index', '', '4', 'nav', '1');
INSERT INTO `ob_auth_rule` VALUES ('47', '46', '还原', 'admin/database/import', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('48', '46', '备份', 'admin/database/backup', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('49', '46', '优化', 'admin/database/optimize', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('50', '46', '修复', 'admin/database/repair', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('51', '46', '下载', 'admin/database/download', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('52', '46', '删除', 'admin/database/del', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('53', '18', '清空', 'admin/admin/truncate', '', '0', 'auth', '1');
INSERT INTO `ob_auth_rule` VALUES ('55', '4', '邮件设置', 'admin/config/email', '', '5', 'nav', '1');

-- -----------------------------
-- Table structure for `ob_config`
-- -----------------------------
DROP TABLE IF EXISTS `ob_config`;
CREATE TABLE `ob_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group` varchar(32) NOT NULL DEFAULT '' COMMENT '配置分组',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '配置标题',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '配置标识',
  `type` varchar(32) NOT NULL DEFAULT '' COMMENT '配置类型',
  `value` text CHARACTER SET utf8 NOT NULL COMMENT '默认值',
  `options` text CHARACTER SET utf8 DEFAULT NULL COMMENT '选项值',
  `sort_order` int(11) NOT NULL DEFAULT 100 COMMENT '排序',
  `size` varchar(32) NOT NULL DEFAULT '' COMMENT '编辑框大小',
  `notes` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT COMMENT='配置';

-- -----------------------------
-- Records of `ob_config`
-- -----------------------------
INSERT INTO `ob_config` VALUES ('2', 'base', '网站名称', 'site_name', 'input', 'OpenBMS', '', '100', 'sm', '', '1');
INSERT INTO `ob_config` VALUES ('3', 'base', '网站标题', 'site_title', 'input', 'OpenBMS 开源后台管理系统', '', '100', 'sm', '', '1');
INSERT INTO `ob_config` VALUES ('4', 'base', '网站关键字', 'site_keywords', 'input', 'OpenBMS,开源后台管理系统', '', '100', 'md', '', '1');
INSERT INTO `ob_config` VALUES ('5', 'base', '网站描述', 'site_description', 'textarea', 'OpenBMS,开源后台管理系统,Open Background Management System 开源后台管理系统', '', '100', 'sm', '', '1');
INSERT INTO `ob_config` VALUES ('7', 'base', 'ICP备案号', 'site_icp', 'input', '', '', '100', 'xs', '', '1');
INSERT INTO `ob_config` VALUES ('10', 'wechat', '微信Logo', 'wechat_logo', 'image', '', '', '100', 'sm', '', '1');
INSERT INTO `ob_config` VALUES ('11', 'wechat', '小程序AppID', 'small_appid', 'input', 'wx9518ed51d1d0068e', '', '100', 'xs', '', '1');
INSERT INTO `ob_config` VALUES ('12', 'wechat', '小程序AppSecret', 'small_appsecret', 'input', 'f0920a949a1ad37353fa9a52afb2e545', '', '100', 'sm', '', '1');
INSERT INTO `ob_config` VALUES ('13', 'wechat', '商户号Mchid', 'mchid', 'input', '', '', '100', 'xs', '', '1');
INSERT INTO `ob_config` VALUES ('14', 'wechat', 'API支付密钥', 'key', 'input', '', '', '100', 'sm', 'API v2密钥', '1');
INSERT INTO `ob_config` VALUES ('15', 'wechat', '商户API私钥', 'apiclient_key', 'file', '', '', '100', 'sm', '', '1');
INSERT INTO `ob_config` VALUES ('16', 'wechat', '支付平台证书', 'apiclient_cert', 'file', '', '', '100', 'sm', '', '1');
INSERT INTO `ob_config` VALUES ('17', 'api', 'API_Key', 'key', 'textarea', 'X9k2L5m8P1q4R7s0T3v6W9y2Z5a8B1c4D7e0F3g6H9j2K5m8N1p4Q7r0', '', '100', 'sm', '', '1');
INSERT INTO `ob_config` VALUES ('18', 'api', 'Token有效期', 'exp', 'input', '7200', '', '100', 'xs', 'access_token有效期（秒）', '1');
INSERT INTO `ob_config` VALUES ('20', 'api', 'Refresh有效期', 'refresh_exp', 'input', '30', '', '100', 'xs', 'refresh_token有效期（天）', '1');

-- -----------------------------
-- Table structure for `ob_system`
-- -----------------------------
DROP TABLE IF EXISTS `ob_system`;
CREATE TABLE `ob_system` (
  `name` varchar(255) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='系统配置';

-- -----------------------------
-- Records of `ob_system`
-- -----------------------------
INSERT INTO `ob_system` VALUES ('administrator', 'admin');
INSERT INTO `ob_system` VALUES ('colse_explain', 'API');
INSERT INTO `ob_system` VALUES ('email_server', 'a:7:{s:4:\"host\";s:0:\"\";s:6:\"secure\";s:3:\"tls\";s:4:\"port\";s:0:\"\";s:8:\"username\";s:0:\"\";s:8:\"password\";s:0:\"\";s:8:\"fromname\";s:0:\"\";s:5:\"email\";s:0:\"\";}');
INSERT INTO `ob_system` VALUES ('page_number', '10');
INSERT INTO `ob_system` VALUES ('upload_image', 'a:2:{s:11:\"upload_size\";s:1:\"5\";s:10:\"upload_ext\";s:37:\"jpg,png,gif,zip,rar,pem,pdf,xlsx,docx\";}');
INSERT INTO `ob_system` VALUES ('website_status', '0');

-- -----------------------------
-- Table structure for `ob_user`
-- -----------------------------
DROP TABLE IF EXISTS `ob_user`;
CREATE TABLE `ob_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `openid` varchar(50) NOT NULL DEFAULT '' COMMENT 'OpenID',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',
  `mobile` char(20) NOT NULL DEFAULT '' COMMENT '手机',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0禁用/1启动',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '上次登录时间',
  `login_count` int(11) NOT NULL DEFAULT 0 COMMENT '登录次数',
  `create_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2014 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC COMMENT='会员';

-- -----------------------------
-- Records of `ob_user`
-- -----------------------------
INSERT INTO `ob_user` VALUES ('1', 'oaKx_19BGcGdDtNd9QEaN63qMuKI', 'lejRej', '13800138000', '1', '1539134525', '1', '1539134389');
INSERT INTO `ob_user` VALUES ('2', '', 'mbk5ez', '18517221367', '1', '0', '2', '948991937');
INSERT INTO `ob_user` VALUES ('3', '', 'nel5aK', '18827052765', '0', '1539676825', '8920', '49504776');
INSERT INTO `ob_user` VALUES ('4', '', 'vbmOeY', '15046082816', '0', '200150694', '2342', '0');
INSERT INTO `ob_user` VALUES ('5', '', 'penRe7', '17617282524', '0', '1423053875', '33', '1219382029');
INSERT INTO `ob_user` VALUES ('6', '', 'xbojag', '13387274906', '0', '0', '435', '1383530265');
INSERT INTO `ob_user` VALUES ('7', '', 'mep2bM', '13239374679', '1', '1407254843', '3', '0');
INSERT INTO `ob_user` VALUES ('8', '', 'zbq2dp', '14712851385', '1', '1345566847', '22', '741575509');
INSERT INTO `ob_user` VALUES ('9', '', 'YerEdO', '13834154444', '0', '345474435', '433', '0');
INSERT INTO `ob_user` VALUES ('10', '', '9avmeG', '13065229793', '1', '1411213764', '4', '0');
INSERT INTO `ob_user` VALUES ('11', '', 'DdwRb1', '13678425083', '0', '362535199', '34', '1001044079');
INSERT INTO `ob_user` VALUES ('12', '', '7ax9by', '15610873971', '0', '0', '734', '684714423');
