<?php
return array(
	'cnt'=>array(//配置在表单中的键名 ,这个会是config[cnt]
		'title'=>'重复几条:',//表单的文字
		'type'=>'radio',		 //表单的类型：text、textarea、checkbox、radio、select等
		'options'=>array(		 //select 和radion、checkbox的子选项
			'2'=>'2条',		 //值=>文字
			'1'=>'1条',
		),
		'value'=>'1',			 //表单的默认值
	),
    'type'=>array(//配置在表单中的键名 ,这个会是config[cnt]
        'title'=>'支持类型:',//表单的文字
        'type'=>'checkbox',		 //表单的类型：text、textarea、checkbox、radio、select等
        'options'=>array(		 //select 和radion、checkbox的子选项
            '2'=>'微信公众号消息',		 //值=>文字
            '1'=>'模版消息',
        ),
        'value'=>'2',			 //表单的默认值
    ),
);
					