SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `itboye_20150606`
--

-- --------------------------------------------------------

--
-- 表的结构 `itboye_product`
--

CREATE TABLE IF NOT EXISTS `itboye_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `wxaccountid` int(11) NOT NULL,
  `name` varchar(128) NOT NULL COMMENT '商品名称',
  `price` decimal(16,2) NOT NULL COMMENT '销售价格',
  `stock` int(11) NOT NULL COMMENT '库存量',
  `sale_num` int(11) NOT NULL COMMENT '销量',
  `thumbnail` int(11) NOT NULL COMMENT '缩略图id',
  `rate_num` int(11) NOT NULL DEFAULT '0' COMMENT '评分次数',
  `detailtplid` int(11) NOT NULL DEFAULT '0' COMMENT '商品详情页模板',
  `detailcontent` text NOT NULL COMMENT '商品详情内容',
  `dis_price` decimal(16,2) NOT NULL COMMENT '促销价',
  `createtime` int(11) NOT NULL COMMENT '创建时间',
  `updatetime` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

