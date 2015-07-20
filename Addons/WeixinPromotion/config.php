<?php
return array(
	'downloadFolder'=>array(//配置在表单中的键名 ,这个会是config[downloadFolder]
		'title'=>'生成的二维码保存位置:',//表单的文字
		'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
		'value'=>'./Uploads/Qrcode',			 //表单的默认值
	),
      'noAuthorizedMsg'=>array(//配置在表单中的键名 ,这个会是config[noAuthorizedMsg]
		'title'=>'无权限时返回文字:',//表单的文字
		'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
		'value'=>'您还未成为族长，不能生成专属二维码！',			 //表单的默认值
	),
     'tmpFolder'=>array(//配置在表单中的键名 ,这个会是config[noAuthorizedMsg]
		'title'=>'临时存放下载的二维码A:',//表单的文字
		'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
		'value'=>'./Temp',			 //表单的默认值
	),
     'bgImg'=>array(//配置在表单中的键名 ,这个会是config[noAuthorizedMsg]
		'title'=>'作为背景的图片B:',//表单的文字
		'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
		'value'=>'./Temp',			 //表单的默认值
	),
      'mergeFolder'=>array(//配置在表单中的键名 ,这个会是config[noAuthorizedMsg]
		'title'=>'合成后的二维码(A+B＋头像合成)文件夹:',//表单的文字
		'type'=>'text',		 //表单的类型：text、textarea、checkbox、radio、select等
		'value'=>'./Uploads/QrcodeMerge',			 //表单的默认值
	)
);
					