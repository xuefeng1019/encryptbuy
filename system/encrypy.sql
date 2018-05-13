CREATE TABLE `eb_category` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `category_name` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '分类名称',
  `category_tag` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '分类标签',
  `category_img` VARCHAR(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '分类图片',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `parent_id` INT(11) NOT NULL DEFAULT '0' COMMENT '分类父ID',
  `is_lowest` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是最下一级',
  `ca_sort` tinyint(1) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci


CREATE TABLE `eb_goods` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `main_title` VARCHAR(128) COLLATE utf8_unicode_ci NOT NULL COMMENT '主标题',
  `sub_title` VARCHAR(128) COLLATE utf8_unicode_ci NOT NULL COMMENT '副标题',
  `goods_banner` VARCHAR(128) COLLATE utf8_unicode_ci NOT NULL COMMENT '宣传图片',
  `sale_begin` TIMESTAMP NULL DEFAULT NULL COMMENT '开始时间',
  `sale_end` TIMESTAMP NULL DEFAULT NULL COMMENT '结束时间',
  `delivery_time` TIMESTAMP NULL DEFAULT NULL COMMENT '发货时间',
  `eth_price` INT(11) NOT NULL DEFAULT '0' COMMENT '价格',
  `eth_delivery_price` INT(11) NOT NULL DEFAULT '0' COMMENT '运费',
  `main_img` text COLLATE utf8_unicode_ci NOT NULL COMMENT '商品主图json',
  `category` INT(11) NOT NULL DEFAULT '0' COMMENT 'category_relateID',
  `goods_limit` INT(11) NOT NULL DEFAULT '0' COMMENT '商品限量总数',
  `buy_limit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '单次购买限量',
  `user_buy_limit` tinyint(11) NOT NULL DEFAULT '0' COMMENT '用户购买限量',
  `slice_count` int(11) not null default '1' comment '如果是一元购，商品被切割成几份',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否上架',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `sold_count` INT(11) NOT NULL DEFAULT '0' COMMENT '已售总数',
  `unit` CHAR(5) COLLATE utf8_unicode_ci DEFAULT '' COMMENT '计量单位',
  `goods_type` tinyint(1) not null default '1' comment '商品类型：1 一元购 2 正常',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci


CREATE TABLE `eb_address` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `user_id` INT(11) UNSIGNED NOT NULL COMMENT '用户id',
  `name` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '姓名',
  `phone_number` VARCHAR(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '联系电话',
  `area` VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '省市区地址',
  `detail_address` VARCHAR(300) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '具体地址',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为默认地址',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci


CREATE TABLE `eb_order` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `user_id` INT(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `address_id` INT(11) NOT NULL DEFAULT '0' COMMENT '收货地址ID',
  `pay_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '​支付方式 1:以太币',
  `pay_price` INT(11) NOT NULL COMMENT '支付金额',
  `delivery_price` INT(11) NOT NULL DEFAULT '0' COMMENT '运费',
  `order_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '订单状态：1 待支付 2 待发货3 已取消 4 未知 5 已发货 6 已收货 7 已完成 8 已退款 9 售后中 10 售后完成',
  `delivery_time` TIMESTAMP NULL DEFAULT NULL COMMENT '发货时间',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `order_sn` VARCHAR(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `refund_time` TIMESTAMP NULL DEFAULT NULL,
  `out_trade_no` VARCHAR(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '外部订单号',
  `pay_time` TIMESTAMP NULL DEFAULT NULL COMMENT '支付时间',
  `is_show` INT(1) DEFAULT '0' COMMENT '0不显示 1显示',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci

CREATE TABLE `eb_order_goods` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `order_id` INT(11) NOT NULL DEFAULT '0' COMMENT '订单ID',
  `goods_id` INT(11) NOT NULL DEFAULT '0' COMMENT '商品ID',
  `goods_count` tinyint(2) NOT NULL DEFAULT '1' COMMENT '购买数量',
  `business_data` text comment '业务参数',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci

CREATE TABLE `eb_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `eth_address` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `avatar` varchar(128) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=171 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;