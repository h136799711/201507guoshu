<?php

return array(

	//显示运行时信息
	'view_filter' => array(
		'Behavior\ShowRuntimeBehavior', 
	), 

    'app_init'=>array(
        'Common\Behavior\InitHookBehavior',
    ),

);
